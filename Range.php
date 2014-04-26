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

  $membersRange = substr($input,1,3);
  $member = explode(',',$membersRange);

  $setMembers = getCloseMembers($member[0],$member[1]);

  $firstSign = $input[0];
  $lastSign = $input[4];

  $signs = $firstSign . $lastSign;
  $lastFive = "," . $member[1];
  $firstZero = $member[0] . ",";

  if($member[0] == $member[1]) {
    $lastFive = "";
    $firstZero = "";
  
    if($signs == "[]") {
        $setMembers = $member[0];
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
