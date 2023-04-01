<?php
/**
* class Validate_Credit_Card
*
* Validates Credit Card Numbers and Expiration Dates
* using the MOD10 Algorithm
* I used the PHP Guy Tutorial to get the alogrithm information.
*
* @author Rick Robinson <dev@terraaccess.com>
* @link http://www.terraaccess.com Terra Access
*
* @package TA_AuthNet
* @version 1.3
*
* @link http://php.inc.ru/main.php?view=tutorials&t=p_cc The PHP Guy Article
* @access public
*/




class Validate_Credit_Card
{

  /**
  * Validate_Credit_Card::$error_text
  *
  * Converts Error Codes to Readable Text
  *
  * @var array
  * @access public
  */
  var $error_text = array (
                            ERR_INVALID_MOD10      => "Failed the Mod10 test."
                           ,ERR_INVALID_PREFIX     => "Card Number Has Invalid Prefix"
                           ,ERR_INVALID_LENGTH     => "Card Number is not the Right Length"
                           ,ERR_NOT_NUMERIC        => "Card Number Must be all Numbers"
                           ,ERR_INVALID_EXPIRATION => "Invalid Expiration Date"
                          );

  /**
  * Validate_Credit_Card::$credit_card_name
  *
  * Converts Credit Card defines to Readable Text
  *
  * @var array
  * @access public
  */
  var $credit_card_name = array (
                                  CC_AMERICAN_EXPRESS => "Amex"
                                 ,CC_DINERS_CLUB      => "Diners Club"
                                 ,CC_DISCOVER         => "Discover"
                                 ,CC_JB               => "JB"
                                 ,CC_MASTER_CARD      => "MasterCard"
                                 ,CC_VISA             => "Visa"
                                );

  /**
  * Validate_Credit_Card::$cards_prefix
  *
  * Stores Valid Prefixes for Credit Cards
  *
  * @var array
  * @access public
  */
  var $cards_prefix = array (
                              CC_AMERICAN_EXPRESS => array ( 34, 37 )
                             ,CC_DINERS_CLUB      => array ( 300, 301, 302, 303, 304, 305, 36, 38 )
                             ,CC_DISCOVER         => array ( 6011 )
                             ,CC_JB               => array ( 3, 1800, 2131 )
                             ,CC_MASTER_CARD      => array ( 51, 52, 53, 54, 55 )
                             ,CC_VISA             => array ( 4 )
                            );

  /**
  * Validate_Credit_Card::$cards_length
  *
  * Stores Valid Lengths for Credit Cards
  *
  * @var array
  * @access public
  */
  var $cards_length = array (
                              CC_AMERICAN_EXPRESS => array ( 15 )
                             ,CC_DINERS_CLUB      => array ( 14 )
                             ,CC_DISCOVER         => array ( 16 )
                             ,CC_JB               => array ( 15, 16 )
                             ,CC_MASTER_CARD      => array ( 16 )
                             ,CC_VISA             => array ( 13, 16 )
                            );

  /**
  * Validate_Credit_Card::$cc_type
  *
  * Stores Credit Card Type for last Number ran
  *
  * @var integer
  * @access public
  */
  var $cc_type;

  /**
  * Validate_Credit_Card::Validate_Credit_Card()
  *
  * Constructor
  *
  */
  function Validate_Credit_Card ()
  {
    $this->cc_type = ERR_UNKNOWN;
  }

  /**
  * Validate_Credit_Card::is_valid_card()
  *
  * Validates credit card number and expiration date
  *
  * @param string $card_number
  * @param string $exp_month
  * @param string $exp_year
  * @return integer CC_SUCCESS or error code
  *
  * @see is_valid_number()
  * @see is_valid_expiration()
  *
  * @access public
  */
  function is_valid_card ( $card_number, $exp_month, $exp_year )
  {
    $ret = $this->is_valid_number ( $card_number );
    if (  $ret != CC_SUCCESS )
    {
      return $ret;
    }

    $ret = $this->is_valid_expiration ( $exp_month, $exp_year );
    if ($ret != CC_SUCCESS )
    {
      return $ret;
    }

    return CC_SUCCESS;

  }
  /**
  * Validate_Credit_Card::is_valid_number()
  *
  * Calls internal functions to validate card number
  *
  * @param string $card_number Credit Card Number
  * @param integer $card_type  Credit Card Company as defines above
  * @return integer ERR_UNKNOWN CC_SUCCESS CC_FAILURE
  *
  * @see get_card_type()
  * @see mod10()
  * @see match_prefix()
  * @see match_lenght()
  * @see $cc_type
  * @access public
  */
  function is_valid_number ( $card_number, $card_type = ERR_UNKNOWN  )
  {
    //if $card_type is unknown then try and get it
    if ( $card_type == ERR_UNKNOWN )
    {
      $card_type = $this->get_card_type ( $card_number );
      //if card type is still unknown then
      //we have a bad credit card number
      if ( $card_type == ERR_UNKNOWN )
      {
        return ERR_UNKNOWN;
      }
    }
    //set the global variable to the credit card
    //type we are now working on
    $this->cc_type = $card_type;

    //check the mod10 value of the credit card number
    if ( $this->mod10 ( $card_number ) != CC_SUCCESS )
    {
      return ERR_INVALID_MOD10;
    }

    //ensure the prefix is valid for the type of card
    //this must be done before checking the credit card
    //number length as it requires a card type to check
    //against
    if ( $this->match_prefix ( $card_number ) != $card_type )
    {
      return ERR_INVALID_PREFIX;
    }

    //ensure the length of the number is valid for the type of card
    if ( $this->match_length ( $card_number, $card_type ) != $card_type )
    {
      return ERR_INVALID_LENGTH;
    }

    //passed all tests
    return CC_SUCCESS;
  }

  /**
  * Validate_Credit_Card::is_valid_expiration()
  *
  * Attempts to validate the expiration date
  * of the credit card. Accepts number parameters only.
  * $year can be in either 4 digit format (2003) or
  * 2 digit format (03)
  *
  * @param string $month 2 (01) or 1 (1) digit format
  * @param string $year  2 (03) or 4 (2003) digit format
  * @return integer ERR_INVALID_EXPIRATION or CC_SUCCESS
  *
  * @access public
  *
  */
  function is_valid_expiration ( $month, $year )
  {
    //if the month or year are not numeric...skipit
    if ( ( !is_numeric ( $month ) ) || ( !is_numeric ( $year ) ) )
    {
      return ERR_INVALID_EXPIRATION;
    }

    //set the current year to the year format sent in
    //and convert to integer
    //if length is 2 (03) or 4 (2003) else...skipit
    if ( strlen ( $year ) == 4 )
    {
      $current_year = (integer) date ( 'Y' );
    }
    elseif ( strlen ( $year ) == 2 )
    {
      $current_year = (integer) date ( 'y' );
    }else{
      return ERR_INVALID_EXPIRATION;
    }

    //convert the curent month to integer
    $current_month = (integer) date ( 'm' );

    //valid values for month are 1 thru 12
    if ( ( $month < 1 ) || ( $month > 12 ) )
    {
      return ERR_INVALID_EXPIRATION;
    }

    //if the year passed in is before the current year of
    //more than 10 years out...skipit
    if ( ( $year < $current_year ) || ( $year > ( $current_year + 10 ) ) )
    {
      return ERR_INVALID_EXPIRATION;
    }

    //if the year passed in is the same as the current year
    //and the month is before the current month...skipit
    if ( ( $year == $current_year ) && ( $month < $current_month ) )
    {
      return ERR_INVALID_EXPIRATION;
    }

    //passed all tests, expiration date is ok
    return CC_SUCCESS;
  }

  /**
  * Validate_Credit_Card::mod10()
  *
  * Uses mod10 alogorithm as described in The PHP Guy article
  *   Steps:
  *         1. Reverse the number
  *         2. Multiply the even placed digits by 2
  *         3. If the result is more than one digit then
  *            add the digits together
  *         4. Add the odd placed digits in the credit card number together
  *         5. Add the result of #3 and #4 above
  *         6. If the modulas 10 of the result equals 0 then
  *            the number is valid
  *
  *
  * @param string $card_number Credit Card Number
  * @return integer ERR_NOT_NUMERIC ERR_INVALID_MOD10 CC_SUCCESS
  *
  * @access public
  */
  function mod10 ( $card_number )
  {

    $digit_array = array ();
    $cnt = 0;

    //Check to make sure card number is numeric:))
    if ( !is_numeric ( $card_number ) )
    {
      return ERR_NOT_NUMERIC;
    }
    //Reverse the card number
    $card_temp = strrev ( $card_number );

    //Multiple every other number by 2 then ( even placement )
    //Add the digits and place in an array
    for ( $i = 1; $i <= strlen ( $card_temp ) - 1; $i = $i + 2 )
    {
      //multiply every other digit by 2
      $t = substr ( $card_temp, $i, 1 );
      $t = $t * 2;
      //if there are more than one digit in the
      //result of multipling by two ex: 7 * 2 = 14
      //then add the two digits together ex: 1 + 4 = 5
      if ( strlen ( $t ) > 1 )
      {
        //add the digits together
        $tmp = 0;
        //loop through the digits that resulted of
        //the multiplication by two above and add them
        //together
        for ( $s = 0; $s < strlen ( $t ); $s++ )
        {
          $tmp = substr ( $t, $s, 1 ) + $tmp;
        }
      }else{  // result of (* 2) is only one digit long
        $tmp = $t;
      }
      //place the result in an array for later
      //adding to the odd digits in the credit card number
      $digit_array [ $cnt++ ] = $tmp;
    }
    $tmp = 0;

    //Add the numbers not doubled earlier ( odd placement )
    for ( $i = 0; $i <= strlen ( $card_temp ); $i = $i + 2 )
    {
      $tmp = substr ( $card_temp, $i, 1 ) + $tmp;
    }

    //Add the earlier doubled and digit-added numbers to the result
    $result = $tmp + array_sum ( $digit_array );

    //Check to make sure that the remainder
    //of dividing by 10 is 0 by using the modulas
    //operator
    if ( $result % 10 == 0 )
    {
      return CC_SUCCESS;
    }else{
      return ERR_INVALID_MOD10;
    }

  }

  /**
  * Validate_Credit_Card::get_card_type()
  *
  * Returns the card type based on credit card number
  * Has to match prefix and then length much match based
  * on prefix match
  *
  * @param string $card_number Credit Card Number
  * @return integer ERR_UNKNOWN or integer that matches defines for Credit Cards
  *
  * @see match_prefix()
  * @see match_length()
  * @access public
  *
  */
  function get_card_type ( $card_number )
  {
    //try and match up the card number against
    //know valid prefix values
    $prefix_ret = $this->match_prefix ( $card_number );
    if ( $prefix_ret == ERR_UNKNOWN )
    {
      return ERR_UNKNOWN;

    }

    //using the card number and card type check to make sure
    //the card number is a valid length
    $length_ret = $this->match_length ( $card_number, $prefix_ret );

    //if the card type returned by both functions is
    //the same then we have a valid credit card number
    if ( $length_ret == $prefix_ret )
    {
      //set the internal global card type to
      //the current card number type and return it
      $this->cc_type = $prefix_ret;
      return $prefix_ret;
    }
    //set the internal global card type to
    //unknown and return that fact
    $this->cc_type = ERR_UNKNOWN;
    return ERR_UNKNOWN;

  }

  /**
  * Validate_Credit_Card::match_prefix()
  *
  * Finds Credit Card Type base on prefix match alone
  *
  * @param string $card_number
  * @return integer ERR_UNKNOWN or integer that matches defines for Credit Cards
  *
  * @access public
  */
  function match_prefix ( $card_number )
  {
    //initialize to first card type
    $tmp_type = 1;
    //loop through the card types
    foreach ( $this->cards_prefix as $card_array )
    {
      //loop through the valid prefixes for each card
      //type until a match is found
      foreach ( $card_array as $prefix )
      {
        //get the numbers from the card number that
        //match the length of the valid prefix
        $tmp = substr ( $card_number, 0, strlen ( $prefix ) );

        //if the retrieved card numbers match the
        //valid prefix return the card type matched
        if ( $tmp == $prefix )
        {
          return $tmp_type;
        }
      }
      //move on to the next card type
      $tmp_type++;
    }
    //sorry we didn't find any prefixes that
    //match your card number
    return ERR_UNKNOWN;

  }

  /**
  * Validate_Credit_Card::match_length()
  *
  * Ensures the length of the credit card number matches
  * the published allowed lengths. I use this just to verify
  * once I have the Card Type
  *
  * @param string $card_number
  * @return integer ERR_UNKNOWN or integer that matches defines for Credit Cards
  *
  * @access public
  */
  function match_length ( $card_number, $card_type )
  {
    //get card number length
    $tmp_length = strlen ( $card_number );

    //set the working array to the card type passed in
    $card_array = $this->cards_length [ $card_type ];

    //loop through the valid lengths for specified card
    //type until a match is found
    foreach ( $card_array as $valid_length )
    {
      //if the retrieved length matches the length
      //of the card number passed in then return card type
      if ( $tmp_length == $valid_length )
      {
        return $card_type;
      }
    }

    //sorry we didn't find any lengths in the
    //card type you specified that match the
    //credit card number length
    return ERR_UNKNOWN;

  }

  /**
  * Validate_Credit_Card::get_error_text()
  *
  * Outputs text for Error Codes Listed in defines
  *
  * @param integer $errno
  * @return string Error Text
  *
  * @see $error_text
  * @access public
  */
  function get_error_text ( $errno )
  {
    if ( isset ( $this->error_text [ $errno ] ) )
    {
      return $this->error_text [ $errno ];
    }else{
      return "Invalid Error Code";
    }
  }

  /**
  * Validate_Credit_Card::get_credit_card_name()
  *
  * Returns Proper Name for Last Credit Card Validated
  *
  * @return string Credit Card Company Name
  *
  * @see $credit_card_name
  * @access public
  */
  function get_credit_card_name ()
  {

    return $this->credit_card_name [ $this->cc_type ];

  }

}  //END CLASS Validate_Credit_Card
?>


