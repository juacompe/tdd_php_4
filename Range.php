<?php

function calRange($input) {
  $inputParser = new InputParser($input);
  $leftBorder = $inputParser->leftBorder();
  $rightBorder = $inputParser->rightBorder();

  $set = new Set($leftBorder, $rightBorder);
  $setMembers = $set->membersInBetween();

  $lastFive = "," . $rightBorder;
  $firstZero = $leftBorder . ",";

  if($leftBorder == $rightBorder) {
    $lastFive = "";
    $firstZero = "";
  
    if($inputParser->isCloseClose()) {
        $set = new SetWithOneMember($leftBorder, $rightBorder);
        $setMembers = $set->membersInBetween();
    } else if ($inputParser->isOpenOpen()) {
        $set = new EmptySet($leftBorder, $rightBorder);
        $setMembers = $set->membersInBetween();
    } else {
        throw new Exception("invalid");
    }
  }

  if($inputParser->isOpenClose())  {
    $setMembers = $setMembers . $lastFive;

  } else if($inputParser->isCloseOpen()) {
    $setMembers = $firstZero . $setMembers;

  } else if($inputParser->isCloseClose()){
    $setMembers = $firstZero . $setMembers . $lastFive;
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

class EmptySet extends Set {
  function membersInBetween() {
    return "";
  }
}

class SetWithOneMember extends Set {
  function membersInBetween() {
    return $this->lowBorder; 
  }
}

class Set {
  protected $lowBorder;
  protected $highBorder;

  function __construct($lowBorder, $highBorder) {
    $this->lowBorder = $lowBorder;
    $this->highBorder = $highBorder;
  }

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
