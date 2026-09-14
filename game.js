/**
 * game.js — Tic-Tac-Toe game logic + Minimax AI with alpha-beta pruning
 *
 * Runs entirely in the browser. The server only saves results via AJAX.
 */

(function () {
    "use strict";

    // --- Constants ---
    var PLAYER = "X";
    var AI     = "O";
    var EMPTY  = "";

    var WINNING_COMBOS = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],  // rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8],  // columns
        [0, 4, 8], [2, 4, 6]               // diagonals
    ];

    // --- State ---
    var board     = [EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY];
    var gameOver  = false;
    var playerTurn = true;

    // --- DOM references ---
    var cells      = document.querySelectorAll(".cell");
    var statusEl   = document.getElementById("status");
    var newGameBtn = document.getElementById("newGameBtn");

    // --- Attach click handlers to each cell ---
    cells.forEach(function (cell) {
        cell.addEventListener("click", function () {
            var index = parseInt(cell.getAttribute("data-index"));
            playerMove(index);
        });
    });

    newGameBtn.addEventListener("click", resetGame);

    // ============================================================
    // Player move
    // ============================================================
    function playerMove(index) {
        if (gameOver || !playerTurn || board[index] !== EMPTY) return;

        board[index] = PLAYER;
        render();
        if (checkGameOver()) return;

        // AI plays after a short delay so it feels natural
        playerTurn = false;
        statusEl.textContent = "AI is thinking...";

        setTimeout(function () {
            aiMove();
            render();
            checkGameOver();
            playerTurn = true;
        }, 300);
    }

    // ============================================================
    // AI move via Minimax
    // ============================================================
    function aiMove() {
        var bestScore = -Infinity;
        var bestIndex = -1;

        for (var i = 0; i < 9; i++) {
            if (board[i] === EMPTY) {
                board[i] = AI;
                var score = minimax(board, 0, false, -Infinity, Infinity);
                board[i] = EMPTY;
                if (score > bestScore) {
                    bestScore = score;
                    bestIndex = i;
                }
            }
        }

        if (bestIndex !== -1) {
            board[bestIndex] = AI;
        }
    }

    // ============================================================
    // Minimax with alpha-beta pruning
    // ============================================================
    function minimax(b, depth, isMaximizing, alpha, beta) {
        var winner = getWinner(b);
        if (winner === AI)  return 10 - depth;
        if (winner === PLAYER) return depth - 10;
        if (getAvailable(b).length === 0) return 0;

        if (isMaximizing) {
            var maxEval = -Infinity;
            for (var i = 0; i < 9; i++) {
                if (b[i] === EMPTY) {
                    b[i] = AI;
                    var eval_ = minimax(b, depth + 1, false, alpha, beta);
                    b[i] = EMPTY;
                    maxEval = Math.max(maxEval, eval_);
                    alpha = Math.max(alpha, eval_);
                    if (beta <= alpha) break;
                }
            }
            return maxEval;
        } else {
            var minEval = Infinity;
            for (var j = 0; j < 9; j++) {
                if (b[j] === EMPTY) {
                    b[j] = PLAYER;
                    var eval2 = minimax(b, depth + 1, true, alpha, beta);
                    b[j] = EMPTY;
                    minEval = Math.min(minEval, eval2);
                    beta = Math.min(beta, eval2);
                    if (beta <= alpha) break;
                }
            }
            return minEval;
        }
    }

    // ============================================================
    // Helper functions
    // ============================================================
    function getWinner(b) {
        for (var i = 0; i < WINNING_COMBOS.length; i++) {
            var a = WINNING_COMBOS[i][0];
            var c = WINNING_COMBOS[i][1];
            var d = WINNING_COMBOS[i][2];
            if (b[a] !== EMPTY && b[a] === b[c] && b[c] === b[d]) {
                return b[a];
            }
        }
        return null;
    }

    function getAvailable(b) {
        var moves = [];
        for (var i = 0; i < 9; i++) {
            if (b[i] === EMPTY) moves.push(i);
        }
        return moves;
    }

    function render() {
        cells.forEach(function (cell, i) {
            cell.textContent = board[i];
            cell.className = "cell";
            if (board[i] === PLAYER) cell.classList.add("x");
            if (board[i] === AI)     cell.classList.add("o");
            if (board[i] !== EMPTY)  cell.classList.add("taken");
        });
    }

    function checkGameOver() {
        var winner = getWinner(board);
        if (winner) {
            gameOver = true;
            if (winner === PLAYER) {
                statusEl.textContent = "You win!";
                statusEl.className = "status-banner win";
                saveResult("win");
            } else {
                statusEl.textContent = "AI wins!";
                statusEl.className = "status-banner loss";
                saveResult("loss");
            }
            newGameBtn.style.display = "inline-block";
            return true;
        }
        if (getAvailable(board).length === 0) {
            gameOver = true;
            statusEl.textContent = "It's a draw!";
            statusEl.className = "status-banner draw";
            saveResult("draw");
            newGameBtn.style.display = "inline-block";
            return true;
        }
        statusEl.textContent = "Your turn — place X";
        statusEl.className = "status-banner";
        return false;
    }

    function resetGame() {
        board = [EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY];
        gameOver = true;   // will be set false after render
        playerTurn = true;
        newGameBtn.style.display = "none";

        // Reset UI
        cells.forEach(function (cell) {
            cell.textContent = "";
            cell.className = "cell";
        });
        statusEl.textContent = "Your turn — place X";
        statusEl.className = "status-banner";

        // Re-enable
        gameOver = false;
    }

    // ============================================================
    // Save result to server via AJAX (POST to api.php)
    // ============================================================
    function saveResult(result) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "api.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log("Result saved:", result);
                } else {
                    console.error("Failed to save result");
                }
            }
        };
        xhr.send("action=save_result&result=" + encodeURIComponent(result));
    }

})();
