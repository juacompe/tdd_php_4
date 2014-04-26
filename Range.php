<?php

function calRange($input) {
  $inputParser = new InputParser($input);
  $input = $inputParser->getInput();
  $leftBorder = $input->leftBorder;
  $rightBorder = $input->rightBorder;
  $sign = $input->sign;

  throwExceptionIfSetIsNotValid($sign, $leftBorder, $rightBorder);
  return createSet($sign, $leftBorder, $rightBorder)->toString();
}

function throwExceptionIfSetIsNotValid($sign, $leftBorder, $rightBorder) {
  if($leftBorder == $rightBorder) {
    if(!$sign->isCloseClose() && !$sign->isOpenOpen())
        throw new Exception("invalid");
  }
}

function createSet($sign, $leftBorder, $rightBorder) {
  if($sign->isOpenClose())
    return new HighBorderIncludedSet($leftBorder, $rightBorder);
  else if($sign->isCloseOpen())
    return new LowBorderIncludedSet($leftBorder, $rightBorder);
  else if($sign->isCloseClose())
    return $leftBorder==$rightBorder? 
      new SetWithOneMember($leftBorder, $rightBorder):
      new Set($leftBorder, $rightBorder);
  else
    return $leftBorder==$rightBorder? 
      new EmptySet($leftBorder, $rightBorder):
      new NoBorderSet($leftBorder, $rightBorder);
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
