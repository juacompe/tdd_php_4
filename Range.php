<?php

function calRange($input) {
  $input = (new InputParser($input))->getInput();
  throwExceptionIfSetIsNotValid($input);
  return createSet($input)->toString();
}

function throwExceptionIfSetIsNotValid($input) {
  if(isNotValidRange($input))
    throw new Exception("invalid");
}

function isNotValidRange($input) {
    return $input->leftBorder == $input->rightBorder
      && !$input->sign->isCloseClose() 
      && !$input->sign->isOpenOpen();
}

function createSet($input) {
  if($input->sign->isOpenClose())
    return new HighBorderIncludedSet($input->leftBorder, $input->rightBorder);
  else if($input->sign->isCloseOpen())
    return new LowBorderIncludedSet($input->leftBorder, $input->rightBorder);
  else if($input->sign->isCloseClose())
    return $input->leftBorder==$input->rightBorder? 
      new SetWithOneMember($input->leftBorder, $input->rightBorder):
      new Set($input->leftBorder, $input->rightBorder);
  else
    return $input->leftBorder==$input->rightBorder? 
      new EmptySet($input->leftBorder, $input->rightBorder):
      new NoBorderSet($input->leftBorder, $input->rightBorder);
}

class InputParser {
  private $input;
  private $firstSign;
  private $lastSign;

  function __construct($input) { 
    $this->input = $input;
    $this->firstSign = $input[0];
    $this->lastSign = $input[4];

  } 

  function leftBorder() {
    return $this->border()[0];
  }

  function rightBorder() {
    return $this->border()[1];
  }

  function border() {
    $membersRange = substr($this->input,1,3);
    return explode(',',$membersRange);
  }

  function signs() {
    return $this->firstSign . $this->lastSign;
  }

  function getInput() {
    $input = new Input();
    $input->leftBorder = $this->leftBorder();
    $input->rightBorder = $this->rightBorder();
    $input->sign = new Sign($this->signs());
    return $input;
  }
}

class Input {
  public $leftBorder;
  public $rightBorder;
  public $sign;
}

class Sign {
  private $signs;

  function __construct($signs) {
    $this->signs = $signs;
  }

  function isOpenOpen() {
    return $this->signs == "()";
  }

  function isCloseClose() {
    return $this->signs == "[]";
  }

  function isOpenClose() {
    return $this->signs == "(]";
  }

  function isCloseOpen() {
    return $this->signs == "[)";
  }
}

class EmptySet {
  protected $lowBorder;
  protected $highBorder;

  function __construct($lowBorder, $highBorder) {
    $this->lowBorder = $lowBorder;
    $this->highBorder = $highBorder;
  }

  function membersInBetween() {
    return "";
  }

  function toString() {
    return "{" . $this->membersInBetween() . "}";
  }
}

class SetWithOneMember extends EmptySet {
  function membersInBetween() {
    return $this->lowBorder; 
  }
}

class NoBorderSet extends EmptySet {
  function membersInBetween() {
    if($this->lowBorder == $this->highBorder){
      return "";
    }
    for($i=$this->lowBorder+1; $i< $this->highBorder; $i++){
      $result[] = $i;
    }
    return implode($result,',');
  }
}

class Set extends NoBorderSet {
  function higherBound() {
    return "," . $this->highBorder;
  }

  function lowerBound() {
    return $this->lowBorder . ",";
  }

  function members() {
    return $this->lowerBound() . $this->membersInBetween() . $this->higherBound();
  }

  function toString() {
    return "{" . $this->members() . "}";
  }
}

class LowBorderIncludedSet extends Set {
  function members() {
    return $this->lowerBound() . $this->membersInBetween();
  }
}

class HighBorderIncludedSet extends Set {
  function members() {
    return $this->membersInBetween() . $this->higherBound();
  }
}
