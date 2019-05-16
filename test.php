<?PHP

// BAD IDEA, should rather find a way to use way less ram.
ini_set("memory_limit", "2048M");

// Some global variable to clarify code.
define("NONE", 0);
define("CROSS", 1);
define("CIRCLE", 2);

/**
 * TicTacToe class that represent a board of tic tac toe game.
 */
class TicTacToe{

    /**
     * Create a tic tac toe board.
     */
    public function __construct(){
        $this->board_ = array(array(NONE,NONE,NONE), array(NONE,NONE,NONE), array(NONE,NONE,NONE));
    }

    /**
     * Check if a board is curently valid.
     * @return bool True if the board $configuration is valid, false otherwise.
     */
    public function isValid(): bool{
        return ($this->count(CROSS) == $this->count(CIRCLE)) || 
                ($this->count(CROSS) == ($this->count(CIRCLE) + 1));
    }

    /**
     * Count the number of $move in the board $configuration.
     * @param int $move 
     * @return int the number of $move in $configuration.
     */
    public function count(int $move): int{
        $count = 0;
        foreach($this->board_ as $line){
            foreach($line as $cell){
                if ($cell == $move){
                    ++$count;
                }
            }
        }
        return $count;
    }

    /**
     * Function that return who have won the game. 
     * @return int 0 if no one won yet, 1 if cross won, 2 if circle won.
     */
    public function win(): int{
        return max($this->win_line($this->board_), $this->win_column($this->board_), $this->win_diagonal($this->board_));
    }

    /**
     * Say if a line have three consecutive cross or circle.
     * @return int 0 if no one have three consecutive cross or circle, 1 if cross does, 2 otherwise.
     */
    public function win_line(): int{
        for ($i = 0; $i < 3; ++$i){
            if (($this->board_[0][$i] == $this->board_[1][$i]) && ($this->board_[1][$i] == $this->board_[2][$i]) && $this->board_[0][$i] > 0){
                return $this->board_[0][$i];
            }
        }
        return NONE;
    }

    /**
     * Tell if a column have three consecutive cross or circle.
     * @return int 0 if no one have three consecutive cross or circle, 1 if cross does, 2 otherwise.
     */
    public function win_column(): int{
        for ($i = 0; $i < 3; ++$i){
            if (($this->board_[$i][0] == $this->board_[$i][1]) && ($this->board_[$i][1] == $this->board_[$i][2]) && $this->board_[$i][0] > 0){
                return $this->board_[$i][0];
            }
        }
        return NONE;
    }

    /**
     * Tell if someone win diagonally.
     * @return int 0 if no one win, 1 if cross does, 2 otherwise.
     */
    public function win_diagonal(): int{
        if (($this->board_[0][0] == $this->board_[1][1]) && ($this->board_[1][1] == $this->board_[2][2]) && $this->board_[0][0] > 0){
            return $this->board_[0][0];
        }

        if (($this->board_[2][0] == $this->board_[1][1]) && ($this->board_[1][1] == $this->board_[0][2]) && $this->board_[2][0] > 0){
            return $this->board_[2][0];
        }
        return NONE;
    }

    /**
     * Return this TicTacToe board ID. The id represent this particular tic tac toe, 
     * and a tic tac toe can be load from an ID.
     * @return string This tic tac toe ID.
     */
    public function id(): string{
        $id = "";
        for ($i = 0; $i < 9; ++$i){
            $id .= $this->at($i);
        }
        return $id;
    }

    /**
     * Convert a single dimension value to a two dimension values, to access the right cell in the board.
     * $i must be in the interval [0, 9].
     * 0 is the first row and first column, 1 is the first row, second column, and so on.
     * @param int $i The index we want to convert.
     * @return array The result of the conversion. 
     */
    public static function index(int $i): array{
        return [floor($i / 3), $i % 3];
    }

    /**
     * Return the content of the cell at the index $i.
     * @param int $i The index, must be between [0, 9].
     * @return int The content of the cell at the index $i.
     */
    private function at(int $i): int{
        $index = $this->index($i);
        return $this->board_[$index[0]][$index[1]];
    }

    /**
     * Set the cell $i to the value $val.
     * @param int $i The index we want to update. 
     * @param int $val The value we want to put at the index $i.
     */
    private function set(int $i, int $val): void{
        $index = $this->index($i);
        $this->board_[$index[0]][$index[1]] = $val;
    }

    /**
     * Generate a set of all the next possible moves the the next player.
     * @return array The set of all the next possible moves.
     */
    public function nextIteration(): array{
        $result = array();
        $nb_empty = $this->count(NONE);

        if ($nb_empty != 0){
            $next_player = $this->nextPlayer();
            $index = 0;
            for($i = 0; $i < $nb_empty; ++$i){
                $nextTicTacToe = new TicTacToe();
                $nextTicTacToe->fromId($this->id());
                while (($nextTicTacToe->at($index) != NONE)){
                    ++$index;
                }
                $nextTicTacToe->set($index, $next_player);
                array_push($result, $nextTicTacToe);
                ++$index;
            }
        }
        return $result;
    }

    /**
     * Function that say wether the cell at [$i, $j] is empty, 
     * and therefore if this is a valid move.
     * @param int $i The concerned row.
     * @param int $j The concerned column.
     * @return bool True if this is a valid move, false otherwise.
     */
    public function canPlay(int $i, int $j): bool{
        return $this->board_[$i][$j] == NONE;
    }

    /**
     * Check if the board is filled or not.
     * @return bool True if the board is filled, false otherwise.
     */
    public function ended(): bool{
        return $this->count(NONE) == 0;
    }

    /**
     * Load a tic tac toe board from an ID.
     * @param string $id The tic tac toe ID that we want to load.
     */
    public function fromId(string $id): void{
        for ($i = 0; $i < 9; ++$i){
            $this->set($i, (int)$id[$i]);
        }
    }

    /**
     * Tell who is the next player.
     * @return int 1 if cross have to play, 2 if circle have to play.
     */
    public function nextPlayer(): int{
        return ($this->count(CROSS) - $this->count(CIRCLE)) == 0 ? CROSS : CIRCLE;
    }

    /**
     * Set the board at the row $i column $j to nextPlayer().
     * @param int $i The concerned row.
     * @param int $j The concerned column.
     * @return bool True if the move was done, false otherwise.
     */
    public function play(int $i, int $j): bool{
        $played = false;
        if ($this->canPlay($i, $j)){
            $this->board_[$i][$j] = $this->nextPlayer();
            $played = true;
        }
        return $played;
    }

    /**
     * Return an random empty cell.
     * @return int The index of the random empty cell.
     */
    public function randomEmpty(): int{
        $empty_position = array();
        for($i = 0; $i < 9; ++$i){
            if ($this->at($i) == NONE){
                array_push($empty_position, $i);
            }
        }
        return $empty_position[rand(0, count($empty_position) - 1)];
    }

    /**
     * Play on a random empty cell.
     */
    public function randomMove(): void{
        $index = $this->index($this->randomEmpty());
        $this->play($index[0], $index[1]);
    }
}

/**
 * Base class for Graph representation.
 * Not useful like so, and quite empty. Should clean this up.
 * Use matrice to represent graphs.
 */
class Graph{
    /**
     * Build a graph.
     * @param int $node the number of node in the graph.
     */
    public function __construct(int $node){
        $this->body_ = array_fill(0, $node, array_fill(0, $node, 0));
    }
}

/**
 * Oriented graph class, inherit from graph.
 */
class OrientedGraph extends Graph{
    /**
     * Build an oriented graph.
     * @param $node The number of node in the graph.
     */
    public function __construct(int $node){
        parent::__construct($node);
    }

    /**
     * Add an edge between $a and $b.
     * @param int $a the first vertice.
     * @param int $b the second vertice.
     */
    public function addEdge(int $a, int $b){
        $this->body_[$a][$b] = 1;
    }

    /**
     * Remove the edge between $a and $b
     * @param int $a the first vertice.
     * @param int $b the second vertice.
     */
    public function removeEdge(int $a, int $b){
        $this->body_[$a][$b] = 0;
    }

    /**
     * Get the set neighborhood of $a.
     * @param int $a a vertice.
     * @return array The neighboors of $a.
     */
    public function neighbor(int $a): array{
        $neighbor = array();
        foreach ($this->body_[$a] as $noeud => $linked){
            if ($linked > 0){
                array_push($neighbor, $noeud);
            }
        }
        return $neighbor;
    }

    /**
     * Return the edge value between the vertex $a and $b.
     * @param int First vertice.
     * @param int Second vertice.
     * @return int The edge between $a and $b.
     */
    public function getEdge(int $a, int $b): int{
        return $this->body_[$a][$b];
    }

    /**
     * Compute the dijkstra shortest path starting from $a. 
     * @param int $a The starting vertice.
     * @return array The path array from $a to any other connected vertex.
     */
    public function dijkstraShortestPath(int $a): array{
        $previous = array_fill(0, count($this->body_), -1);
        $distance = array_fill(0, count($this->body_), INF);
        $q = array();
        foreach($this->body_ as $key=>$value){
            array_push($q, $key);
        }
        $distance[$a] = 0;
        while (count($q) > 0){
            $u = 0;
            $min = INF;
            foreach($q as $key=>$value){
                if ($distance[$value] < $min){
                    $u = $value;
                    $min = $distance[$value];
                }
            }
            array_splice($q, array_search($u, $q), 1);
            foreach($this->neighbor($u) as $v){
                if (in_array($v, $q)){
                    $alt = $distance[$u] + $this->getEdge($u, $v);
                    if ($alt < $distance[$v]){
                        $distance[$v] = $alt;
                        $previous[$v] = $u;
                    }
                }
            }
        }
        return $previous;
    }

    /**
     * Bread First Search starting from $source, stopping when $callback function return true.
     * Could loop if $source and result vertex are not linked.
     * @param int $source The starting point of BFS.
     * @param callable $callback The validation function.
     * @return int The result vertex.
     */
    public function bfs(int $source, callable $callback): int{
        $discover = array();
        $file = array($source);
        while(count($file) > 0){
            $v = array_shift($file);
            if ($callback($v)){
                return $v;
            }else{
                foreach($this->neighbor($v) as $w){
                    if (!$discover[$w]){
                        $discover[$w] = true;
                        array_push($file, $w);
                    }
                }
            }
        }
    }
}

/**
 * Generate the graph of all moves.
 * @param TicTacToe The starting tic tac toe board.
 * @param Graph $g The graph in which we put the edges.
 * @return array The correspondance array between tic tac toe ID and vertices values. 
 */
function movesGraph($tictactoe, $g): array{
    $c = array($tictactoe->id() => 0);
    $pile = array($tictactoe);
    $current = null;
    while (count($pile) > 0){
        $current = array_pop($pile);
        $next = $current->nextIteration();
        foreach($next as $ttt_move){
            $move = $ttt_move->id();
            if (!$c[$move]){
                $c[$move] = end($c) +1;
                array_push($pile, $ttt_move);
            }
            $g->addEdge($c[$current->id()], $c[$move]);
        }
    }
    return $c;
}

/**
 * Compute the path from $src to $dest, using the result of the dijkstra shortest path.
 * This function does not check if the path exist, might loop and get stuck here.
 * @param int $src the source 
 * @param int $dest the destination
 * @param array $dsp_previous The dijkstra shortest path result.
 * @return array The path between $src and $dest.
 */
function path(int $src, int $dest, array $dsp_previous): array{
    $result = array();
    $current = $dest;
    while ($current != $src){
        array_push($result, $current);
        $current = $dsp_previous[$current];
    }
    return array_reverse($result);
}

/**
 * Return where is the difference between current tic tac toe game, and the next tic tac toe game
 * to know what to play.
 * @param string The current tic tac toe ID.
 * @param string The next tic tac toe ID.
 * @return array The index of where to play next.
 */
function getMove(string $id_before, string $id_after): array{
    return TicTacToe::index(strspn($id_before ^ $id_after, "\0"));
}

/**
 * Play a tic tac toe game using graphs, dijkstra shortest path and BFS.
 */
function play(){
    $tictactoe = new TicTacToe();
    $g = new OrientedGraph(6100);
    $result = movesGraph($tictactoe, $g);
    print_r(count($result));
    $t = new TicTacToe();
    $t->randomMove();

    /*while ($t->win() == 0){
        $win_result = $g->bfs($result[$t->id()], function($s) use ($result, $t){
            $tt = new TicTacToe();
            $tt->fromId((string)array_search($s, $result));
            return $tt->win() == $t->nextPlayer();
        });
        $goal = array_search($win_result, $result);
        $dsp = $g->dijkstraShortestPath($goal);*/
        /*$path = path($result[$t->id()], $win_result, $dsp[1]);
        $move = getMove((string)array_search($path[count($path) - 2], $result), (string)array_search($path[count($path) - 1], $result));
        $t->play($move[0], $move[1]);*/
    /*}*/
}

/**
 * The main function that start the process.
 */
function main(){
    play();
}
main();
?>