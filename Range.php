<?php

function calRange($input) {
  $inputParser = new InputParser($input);
  $leftBorder = $inputParser->leftBorder();
  $rightBorder = $inputParser->rightBorder();

  $set = new Set($leftBorder, $rightBorder);
  $setMembers = $set->membersInBetween();

  if($leftBorder == $rightBorder) {
    if($inputParser->isCloseClose()) {
        $set = new SetWithOneMember($leftBorder, $rightBorder);
        $setMembers = $set->membersInBetween();
    } else if ($inputParser->isOpenOpen()) {
        $set = new EmptySet($leftBorder, $rightBorder);
        $setMembers = $set->membersInBetween();
    } else {
        throw new Exception("invalid");
    }
    return $set->toString();
  }

  if($inputParser->isOpenClose())  {
    $set = new NoBorderSet($leftBorder, $rightBorder);
    return $set->toString();
  } else if($inputParser->isCloseOpen()) {
    $set = new LowBorderIncludedSet($leftBorder, $rightBorder);
    return $set->toString();
  } else if($inputParser->isCloseClose()){
    return $set->toString();
  }

  return "{" . $setMembers . "}";
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

  function isOpenOpen() {
    return $this->signs() == "()";
  }

  function isCloseClose() {
    return $this->signs() == "[]";
  }

  function isOpenClose() {
    return $this->signs() == "(]";
  }

  function isCloseOpen() {
    return $this->signs() == "[)";
  }

  function signs() {
    return $this->firstSign . $this->lastSign;
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

class Set extends EmptySet {
  function membersInBetween() {
    if($this->lowBorder == $this->highBorder){
      return "";
    }
    for($i=$this->lowBorder+1; $i< $this->highBorder; $i++){
      $result[] = $i;
    }
    return implode($result,',');
  }

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

class NoBorderSet extends Set {
  function members() {
    return $this->membersInBetween() . $this->higherBound();
  }
}
