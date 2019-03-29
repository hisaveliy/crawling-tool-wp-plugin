<?php

use Savellab_Plugin\Utility;

use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase{

   /**
    * @dataProvider inputNumber
    *
    * @return void
    */
   public function testSum($a, $b, $expected){

      $this->assertEquals($expected, Utility::sum($a, $b));
   }

   public function inputNumber(){

      return [
         [2, 2, 4],
         [2.5, 2.5, 5]
      ];
   }

   /**
    * @expectedException InvalidArgumentException
    */
    public function testExceptionIfNonNumericIsPassed(){

      Utility::sum('a', []);
   }
}