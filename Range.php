<?php

function getCloseMembers($firstRange, $lastRange){
  $set = new Set($firstRange, $lastRange);
  return $set->members();
}

function calRange($input) {
  $inputParser = new InputParser($input);
  $leftBorder = $inputParser->leftBorder();
  $rightBorder = $inputParser->rightBorder();

  $set = new Set($leftBorder, $rightBorder);
  $setMembers = $set->members();

  $lastFive = "," . $rightBorder;
  $firstZero = $leftBorder . ",";

  if($leftBorder == $rightBorder) {
    $lastFive = "";
    $firstZero = "";
  
    if($inputParser->isCloseClose()) {
        $setMembers = $leftBorder;
    } else if ($inputParser->isOpenOpen()) {
        $setMembers = "";
    } else if($inputParser->isOpenClose()){
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

class Set {
  private $lowBorder;
  private $highBorder;

  function __construct($lowBorder, $highBorder) {
    $this->lowBorder = $lowBorder;
    $this->highBorder = $highBorder;
  }

  function members() {
    if($this->lowBorder==$this->highBorder){
      return "";
    }
    for($i=$this->lowBorder+1; $i< $this->highBorder; $i++){
      $result[] = $i;
    }
    return implode($result,',');
  }
}
