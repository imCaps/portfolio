/**
 * Created by alexandrshumilow on 27/09/15.
 */

// Field const
const CELL_SIZE = 10, COLS = 60, ROWS = 50;
const AREA_WIDTH = CELL_SIZE * COLS, AREA_HEIGHT = CELL_SIZE * ROWS;

// Snake const
const SPEED = 1, START_XY = [20,35];
const SPEED_INCREASING_INTERVAL = 20; // in seconds
const DIRECTIONS = {
    up: {
        title : 'up',
        reverse_title: 'down',
        key_code: 38
    },
    down: {
        title : 'down',
        reverse_title: 'up',
        key_code: 40
    },
    left: {
        title : 'left',
        reverse_title: 'right',
        key_code: 37
    },
    right: {
        title : 'right',
        reverse_title: 'left',
        key_code: 39
    }
}

var area;

/**
 * Game object
 * @type {{score: number, is_active: boolean, addPoints: Function, stop: Function, start: Function}}
 */
var game = {
    score: 0,
    is_active: false,
    gameOver: false,
    init: function () {
        this.score = 0;
        this.is_active = false;
        this.gameOver = false;
    },
    addPoints: function() {
        this.score += (1 + snake.speed);
    },
    stop: function(is_gameOver) {
        this.is_active = false;
        if (is_gameOver) {
            game.gameOver = true;
            showMessage('Game Over!  Press Enter or Space to play again.');
        } else {
            showMessage('Paused. Press Enter or Space to continue.');
        }
    },
    start: function() {
        removeMessage();
        if (this.gameOver) {
            init();
        } else {
            this.is_active = true;
            gameUpdate();
        }
    },

    drawSnake: function() {
        for (var i = 0; i < snake.body.tail.length; i++) {
            fillCell(snake.body.tail[i], 'white');

        }
    },

    drawFood: function() {
        fillCell(food.position, 'red');
    }

};

/**
 * Food object
 * @type {{x: null, y: null, set: Function, draw: Function}}
 */
var food = {
    position: [],
    set: function() {
        food.position = [Math.ceil(Math.random() * COLS-2) + 1, Math.ceil(Math.random() * ROWS-2) + 1];
    },
    draw: function() {
        game.drawFood();
    }
};

/**
 * Snake object
 * @type {{speed: number, body: {head: {x: null, y: null}, tail: Array}, direction: null, init: Function, move: Function, checkCollision: Function, checkGrowth: Function, draw: Function}}
 */
var snake = {

    speed: 1,
    // FIFO queue, snake's head is in the end of queue
    body: {
        head: {
            x: null,
            y: null
        },
        // includes head position
        tail: []
    },
    direction: null,

    init: function(start_position, direction, speed) {
        this.speed = speed;
        this.direction = direction;
        this.body.head.x = start_position[0];
        this.body.head.y = start_position[1];
        snake.body.tail = [[start_position[0] - 3, start_position[1]], [start_position[0] - 2, start_position[1]], [start_position[0] - 1, start_position[1]], start_position];
        snakeIncreaseSpeed();
    },

    move: function() {
        switch (this.direction) {
            case DIRECTIONS.up:
                this.body.head.y -= 1;
                break;
            case DIRECTIONS.down:
                this.body.head.y += 1;
                break;
            case DIRECTIONS.left:
                this.body.head.x -= 1;
                break;
            case DIRECTIONS.right:
                this.body.head.x += 1;
                break;
        }

        this.checkCollision();
        this.checkGrowth();

        this.body.tail.push([this.body.head.x, this.body.head.y]);

    },

    checkCollision: function() {
        if (this.body.head.x > COLS
            || this.body.head.x < 0
            || this.body.head.y > ROWS
            || this.body.head.y < 0
            || snake.body.tail.contains([this.body.head.x,this.body.head.y])
        ) {
            game.stop(true);
        }
    },

    checkGrowth: function() {
        if (this.body.head.x == food.position[0] && this.body.head.y == food.position[1]) {
            game.addPoints();
            food.set()
        } else {
            this.body.tail.shift();
        }
        showScores();
    },

    draw: function() {
        game.drawSnake();
    }
}

/**
 * Snake real time handler for increasing speed every SPEED_INCREASING_INTERVAL
 */
function snakeIncreaseSpeed() {
    var interval = 1;
    if (game.is_active) {
        interval = SPEED_INCREASING_INTERVAL;
        snake.speed++;
    }
    setTimeout(function () {
        snakeIncreaseSpeed();
    }, (interval * 1000));
}

/**
 * Real time handler for processing each frame
 */
function gameUpdate() {
    if (game.is_active) {
        clearField();
        snake.move();
        food.draw();
        snake.draw();
        setTimeout(function() {
            gameUpdate();
        }, (1000 / (snake.speed * 5)));
    }
}

/**
 * Create area
 */
function createField() {

    if (document.contains(document.getElementById("gameArea"))) {
        document.getElementById('gameArea').remove();
    }
    var field = document.getElementById('gameField');

    area = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    area.setAttribute('width', String(AREA_WIDTH));
    area.setAttribute('height', String(AREA_HEIGHT));
    area.setAttribute('id', 'gameArea');
    area.setAttribute('class', 'gameArea');

    field.appendChild(area);
}
/**
 * Clear area
 */
function clearField() {
    while (area.hasChildNodes()) {
        area.removeChild(area.lastChild);
    }

}
/**
 * Drawing rectangles
 * @param position
 * @param color
 */
function fillCell(position, color) {
    var node = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    node.setAttributeNS(null, 'x', String(position[0] * CELL_SIZE));
    node.setAttributeNS(null, 'y', String(position[1] * CELL_SIZE));
    node.setAttributeNS(null, 'width', String(CELL_SIZE));
    node.setAttributeNS(null, 'height', String(CELL_SIZE));
    node.setAttributeNS(null, 'style', 'fill:' + color + ';');
    area.appendChild(node);
}
/**
 * Show scores
 */
function showScores() {
    document.getElementById('speed').innerText = String(snake.speed);
    document.getElementById('score').innerText = String(game.score);
}
/**
 * Show message
 */
function showMessage(msg) {
    document.getElementById('message').innerText = msg;
    document.getElementById('message').style.visibility = "visible";
}
/**
 * Remove message
 */
function removeMessage() {
    document.getElementById('message').innerText = '';
    document.getElementById('message').style.visibility = "hidden";
}




/**
 * Initial class
 */
function init() {
    createField();
    game.init();
    snake.init(START_XY, DIRECTIONS.right, SPEED);
    food.set();
    game.start();
}


Array.prototype.contains = function ( needle ) {
    for (i in this) {
        if (this[i][0] === needle[0] && this[i][1] === needle[1]) return true;
    }
    return false;
}
Object.prototype.getKey = function(value){
    for(var key in this){
        if(this[key] instanceof Object && this[key].key_code == value){
            return this[key];
        }
    }
    return null;
};

document.addEventListener("keydown", function (e) {
    var keyCode = e.keyCode;
    if (keyCode == 32 || keyCode == 13) {
        if (game.is_active) {
            game.stop(false);
        } else {
            game.start();
        }
    } else {
        var direction = DIRECTIONS.getKey(keyCode);
        if (direction && (snake.direction.title != direction.reverse_title)) {
            snake.direction = direction;
        }
    }
}, false);

document.addEventListener("DOMContentLoaded", function(event) {
    init();
});