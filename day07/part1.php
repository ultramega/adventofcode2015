<?php

class Circuit {

    private $nodes = array();

    public function __construct() {
        $lines = file('input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($lines as $line) {
            $parts = explode('->', $line);
            if(count($parts) !== 2) {
                continue;
            }
            $in = trim($parts[0]);
            $out = trim($parts[1]);
            $value = $this->parseInput($in);
            if($value !== null) {
                $this->nodes[$out] = $value;
            }
        }
    }

    private function parseInput($in) {
        $parts = explode(' ', $in);
        $out = null;
        if(count($parts) === 1) {
            $out = new Node($this->createNode($parts[0]));
        } elseif($parts[0] === 'NOT') {
            $out = new Node(new NotGate($this->createNode($parts[1])));
        } else {
            $left = $this->createNode($parts[0]);
            $op = $parts[1];
            $right = $this->createNode($parts[2]);
            switch($op) {
                case 'AND':
                    $out = new Node(new AndGate($left, $right));
                    break;
                case 'OR':
                    $out = new Node(new OrGate($left, $right));
                    break;
                case 'LSHIFT':
                    $out = new Node(new LShiftGate($left, $right));
                    break;
                case 'RSHIFT':
                    $out = new Node(new RShiftGate($left, $right));
                    break;
                default:
                    return null;
            }
        }

        return $out;
    }

    private function createNode($name) {
        if(is_numeric($name)) {
            return new RawValue($name);
        }
        return new NodeHolder($this, $name);
    }

    public function getNode($name) {
        return $this->nodes[$name];
    }

}

interface Component {

    public function output();
}

class Node implements Component {

    private $component;
    private $cached;

    public function __construct(Component $component) {
        $this->component = $component;
    }

    public function output() {
        if(!isset($this->cached)) {
            $this->cached = $this->component->output();
        }
        return $this->cached;
    }

}

class NodeHolder implements Component {

    private $circuit;
    private $name;

    public function __construct(Circuit $circuit, $name) {
        $this->circuit = $circuit;
        $this->name = $name;
    }

    public function output() {
        return $this->circuit->getNode($this->name)->output();
    }

}

class RawValue implements Component {

    private $value;

    public function __construct($value) {
        $this->value = (int)$value;
    }

    public function output() {
        return $this->value;
    }

}

class NotGate implements Component {

    private $in;

    public function __construct($in) {
        $this->in = $in;
    }

    public function output() {
        return ~$this->in->output();
    }

}

abstract class Gate implements Component {

    protected $left;
    protected $right;

    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }

}

class AndGate extends Gate {

    public function output() {
        return $this->left->output() & $this->right->output();
    }

}

class OrGate extends Gate {

    public function output() {
        return $this->left->output() | $this->right->output();
    }

}

class LShiftGate extends Gate {

    public function output() {
        return $this->left->output() << $this->right->output();
    }

}

class RShiftGate extends Gate {

    public function output() {
        return $this->left->output() >> $this->right->output();
    }

}

echo 'Answer: a -> ' . (new Circuit())->getNode('a')->output() . PHP_EOL;
