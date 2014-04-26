<?php

function getCloseMembers($firstRange, $lastRange){
  if($firstRange==$lastRange){
    return "";
  }
  for($i=$firstRange+1; $i< $lastRange; $i++){
    $result[] = $i;
  }
  $result = implode($result,',');
  return $result;
}

function calRange($input) {
  $inputParser = new InputParser($input);
  $firstMember = $inputParser->firstMember();
  $lastMember = $inputParser->lastMember();

  $setMembers = getCloseMembers($firstMember,$lastMember);

  $signs = $inputParser->signs();

  $lastFive = "," . $lastMember;
  $firstZero = $firstMember . ",";

  if($firstMember == $lastMember) {
    $lastFive = "";
    $firstZero = "";
  
    if($inputParser->isCloseClose()) {
        $setMembers = $firstMember;
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

  function firstMember() {
    return $this->member()[0];
  }

  function lastMember() {
    return $this->member()[1];
  }

  function member() {
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
