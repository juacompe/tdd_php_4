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
  
    if($signs == "[]") {
        $setMembers = $firstMember;
    } else if ($signs == "()") {
        $setMembers = "";
    } else if($signs == "(]"){
        throw new Exception("invalid");
    }
  }

  if($signs == "(]")  {
    $setMembers = $setMembers . $lastFive;

  } else if($signs == "[)") {
    $setMembers = $firstZero . $setMembers;

  } else if($signs == "[]"){
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
    return explode(',',$this->membersRange());
  }

  function membersRange() {
    return substr($this->input,1,3);
  }

  function signs() {
    return $this->firstSign . $this->lastSign;
  }

}
