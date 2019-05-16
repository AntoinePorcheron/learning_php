<?PHP
ini_set("memory_limit", "-1");

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
    public function __construct($board = array()){
        $this->board_ = array(array(NONE,NONE,NONE), array(NONE,NONE,NONE), array(NONE,NONE,NONE));
        if (count($board) > 0){
            $this->board_ = $board;
        }
    }

    /**
     * Generate all the possible valid combination of a tic tac toe game.
     * Be careful, might remain some case I haven't thought of that are not "invalid", but
     * you'll never see in a real game.
     */
    public function generateAll(){
        $possibility = array(NONE, CROSS, CIRCLE);

        $line_combinations = array();

        foreach($possibility as $i){
            foreach($possibility as $j){
                foreach($possibility as $k){
                    array_push($line_combinations, array($i, $j, $k));
                }
            }
        }

        $combinations = array();
        foreach($line_combinations as $i){
            foreach($line_combinations as $j){
                foreach($line_combinations as $k){
                    $temporary_ttt = new TicTacToe(array($i, $j, $k));
                    if ($temporary_ttt->isValid()){
                        array_push($combinations, $temporary_ttt);
                    }
                }
            }
        }
        return $combinations;
    }

    /**
     * Check if a board is curently valid.
     * @param array $configuration The board to check. Probably not the best way to do, not quite POO.
     * @return bool True if the board $configuration is valid, false otherwise.
     */
    public function isValid(){
        return ($this->count(CROSS) == $this->count(CIRCLE)) || 
                ($this->count(CROSS) == ($this->count(CIRCLE) + 1));
    }

    /**
     * Count the number of $move in the board $configuration.
     * @param array $configuration
     * @param int $move 
     * @return int the number of $move in $configuration.
     */
    public function count($move){
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
     * @param array 
     * @return int
     */
    public function win(){
        return max($this->win_line($this->board_), $this->win_column($this->board_), $this->win_diagonal($this->board_));
    }

    /**
     * @param array
     * @return int
     */
    public function win_line(){
        for ($i = 0; $i < 3; ++$i){
            if (($this->board_[0][$i] == $this->board_[1][$i]) && ($this->board_[1][$i] == $this->board_[2][$i]) && $this->board_[0][$i] > 0){
                return $this->board_[0][$i];
            }
        }
        return NONE;
    }

    /**
     * @param array
     * @return int
     */
    public function win_column(){
        for ($i = 0; $i < 3; ++$i){
            if (($this->board_[$i][0] == $this->board_[$i][1]) && ($this->board_[$i][1] == $this->board_[$i][2]) && $this->board_[$i][0] > 0){
                return $this->board_[$i][0];
            }
        }
        return NONE;
    }

    /**
     * @param array
     * @return int
     */
    public function win_diagonal(){
        if (($this->board_[0][0] == $this->board_[1][1]) && ($this->board_[1][1] == $this->board_[2][2]) && $this->board_[0][0] > 0){
            return $this->board_[0][0];
        }

        if (($this->board_[2][0] == $this->board_[1][1]) && ($this->board_[1][1] == $this->board_[0][2]) && $this->board_[2][0] > 0){
            return $this->board_[2][0];
        }
        return NONE;
    }

    public function id(){
        $id = "";
        for ($i = 0; $i < 9; ++$i){
            $id .= $this->at($i);
        }
        return $id;
    }

    private function index($i){
        return [floor($i / 3), $i % 3];
    }

    private function at($i){
        $index = $this->index($i);
        return $this->board_[$index[0]][$index[1]];
    }

    private function set($i, $val){
        $index = $this->index($i);
        $this->board_[$index[0]][$index[1]] = $val;
    }

    public function nextIteration(){
        $result = array();
        $nb_empty = $this->count(NONE);

        if ($nb_empty != 0){
            $next_player = $this->nextPlayer();
            $index = 0;
            for($i = 0; $i < $nb_empty; ++$i){
                $nextTicTacToe = new TicTacToe($this->board_);
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

    public function canPlay($i, $j){
        return $this->board_[$i][$j] == NONE;
    }

    public function ended(){
        return $this->count(NONE) == 0;
    }

    public function fromId($id){
        for ($i = 0; $i < 9; ++$i){
            $this->set($i, (int)$id[$i]);
        }
    }

    public function difference($tictactoe){
        $self_id = $this->id();
        $others_id = $tictactoe->id();
        if ($self_id == $others_id){
            return [-1,-1];
        }else{
            $i = 0;
            while ($self_id[$i] == $others_id[$i]){
                ++$i;
            }
            return $this->index($i);
        }
    }

    public function nextPlayer(){
        return ($this->count(CROSS) - $this->count(CIRCLE)) == 0 ? CROSS : CIRCLE;
    }

    public function play($i, $j){
        if ($this->canPlay($i, $j)){
            $this->board_[$i][$j] = $this->nextPlayer();
        }
        return $this->canPlay($i, $j);
    }

    public function randomEmpty(){
        $empty_position = array();
        for($i = 0; $i < 9; ++$i){
            if ($this->at($i) == NONE){
                array_push($empty_position, $i);
            }
        }
        return $empty_position[rand(0, count($empty_position) - 1)];
    }

    public function randomMove(){
        $index = $this->index($this->randomEmpty());
        $this->play($index[0], $index[1]);
    }
}

class Graph{
    public function __construct($node){
        $this->body_ = array_fill(0, $node, array_fill(0, $node, 0));
    }
}

class OrientedGraph extends Graph{
    public function __construct($node){
        parent::__construct($node);
    }

    public function addEdge($a, $b){
        $this->body_[$a][$b] = 1;
    }

    public function removeEdge($a, $b){
        $this->body_[$a][$b] = 0;
    }

    public function neighbor($a){
        $neighbor = array();
        foreach ($this->body_[$a] as $noeud => $linked){
            if ($linked > 0){
                array_push($neighbor, $noeud);
            }
        }
        return $neighbor;
    }

    public function getEdge($a, $b){
        return $this->body_[$a][$b];
    }

    public function dijkstraShortestPath($a){
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
        return array($distance, $previous);
    }

    public function bfs($source, $callback){
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

function movesGraph($tictactoe, $g){
    function rec_ttt($g, $c, $t){
        if (!$t->ended()){
            $next = $t->nextIteration();
            foreach($next as $tt){
                if (!$c[$tt->id()]){
                    if (count($c) == 0){
                        $c[$tt->id()] = 0;
                    }else{
                        $c[$tt->id()] = end($c) + 1;
                    }
                }
                $id = $c[$t->id()];
                $g->addEdge($id, $c[$tt->id()]);
                $c = rec_ttt($g, $c, $tt);
            }
        }
        return $c;
    }
    return rec_ttt($g, array(), $tictactoe);
}

function win($id, $player){
    $ttt = new TicTacToe();
    $ttt.fromId($id);
    return $ttt->win() == $player;
}

function path($src, $dest, $dsp_previous){
    $result = array();
    $current = $dest;
    while ($current != $src){
        array_push($result, $current);
        $current = $dsp_previous[$current];
    }
    return $result;
}

function index($i){
    return [floor($i/3), $i%3];
}

function getMove($id_before, $id_after){
    print_r($id_before . " " . $id_after . PHP_EOL);
    return index(strspn($id_before ^ $id_after, "\0"));
}

function play(){
    $tictactoe = new TicTacToe();
    $g = new OrientedGraph(6100);
    $result = movesGraph($tictactoe, $g);
    $t = new TicTacToe();
    $t->randomMove();

    while ($t->win() == 0){
        $win_result = $g->bfs($result[$t->id()], function($s) use ($result, $t){
            $tt = new TicTacToe();
            $tt->fromId((string)array_search($s, $result));
            return $tt->win() == $t->nextPlayer();
        });
        $goal = array_search($win_result, $result);
        $dsp = $g->dijkstraShortestPath($goal);
        print_r($result[$t->id()] . " " . $t->id() . PHP_EOL);
        print_r();
        /*$path = path($result[$t->id()], $win_result, $dsp[1]);
        $move = getMove((string)array_search($path[count($path) - 2], $result), (string)array_search($path[count($path) - 1], $result));
        $t->play($move[0], $move[1]);*/
    }
}

function main(){
    play();
}
main();
?>