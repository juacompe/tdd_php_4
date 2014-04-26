<?php

function calRange($input) {
  $inputParser = new InputParser($input);
  $leftBorder = $inputParser->leftBorder();
  $rightBorder = $inputParser->rightBorder();
  $sign = new Sign($inputParser->signs());

  if($leftBorder == $rightBorder) {
    if(!$sign->isCloseClose() && !$sign->isOpenOpen())
        throw new Exception("invalid");
  }
  $set = createSet($sign, $leftBorder, $rightBorder);

  return $set->toString();
}

function createSet($sign, $leftBorder, $rightBorder) {
  if($sign->isOpenClose())
    $set = new HighBorderIncludedSet($leftBorder, $rightBorder);
  else if($sign->isCloseOpen())
    $set = new LowBorderIncludedSet($leftBorder, $rightBorder);
  else if($sign->isCloseClose())
    if($leftBorder == $rightBorder)
        $set = new SetWithOneMember($leftBorder, $rightBorder);
    else
      $set = new Set($leftBorder, $rightBorder);
  else
    if($leftBorder == $rightBorder)
      $set = new EmptySet($leftBorder, $rightBorder);
    else
      $set = new NoBorderSet($leftBorder, $rightBorder);
  return $set;
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
