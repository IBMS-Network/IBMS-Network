<?php
abstract class clsContent {

  protected $parser = "";
  protected $request = array ();
  protected $get = array ();
  protected $post = array ();
  protected $config = array ();
  protected $error = NULL;
  protected $inner = array ();

  protected function clsContent() {
    $this->parser = new clsParser ();
    $this->error = new clsError ();
    $this->config = clsCommon::getDomainConfig ();
  }

  public function setParams($get = array(), $post = array(), $request = array(), $inner = array()) {
    $this->get = $get;
    $this->post = $post;
    $this->request = $request;
    $this->inner = $inner;
    if (! empty ( $this->get ["err_num"] )) {
      $this->error->setError ( clsCommon::isInt ( $this->get ["err_num"] ), 2 );
    }
    $this->URLData2Engine ( $this->get );
    $this->URLData2Engine ( $this->post );
    $this->URLData2Engine ( $this->request );
  }

  protected function URLData2Engine(& $arr, $max_page_value = 10000) {
    if (! empty ( $arr ['page'] )) {
      $arr ['page'] = ( integer ) $arr ['page'];
      if ($arr ['page'] > $max_page_value)
        $arr ['page'] = $max_page_value;
    }

    if (! empty ( $arr ['filter_g'] ) && ! empty ( $this->config ['GenreNames'] )) {
      foreach ( $this->config ['GenreNames'] as $key => $value )
        $this->config ['GenreNames'] [strtolower ( $key )] = $value;

      if (! empty ( $this->config ['GenreNames'] [$arr ['filter_g']] ))
        $arr ['filter_g'] = $this->config ['GenreNames'] [$arr ['filter_g']];
    }

    if (! empty ( $arr ['filter_c'] ) && ! empty ( $this->config ['CountryNames'] )) {
      foreach ( $this->config ['CountryNames'] as $key => $value )
        $this->config ['CountryNames'] [strtolower ( $key )] = $value;

      if (! empty ( $this->config ['CountryNames'] [$arr ['filter_c']] ))
        $arr ['filter_c'] = $this->config ['CountryNames'] [$arr ['filter_c']];
    }

    if (! empty ( $arr ['filter_m'] )) {
      $monthes = array ();
      for($i = 1; $i <= 12; $i ++)
        if (strtolower ( $arr ['filter_m'] ) == strtolower ( date ( "M", mktime ( 0, 0, 0, $i, 1, date ( "Y" ) ) ) ))
          $arr ['filter_m'] = $i;
      if ($arr ['filter_m'] > 12)
        $arr ['filter_m'] = 0;
    }

    if (! empty ( $arr ['filter_d'] ) && $arr ['filter_d'] > 31)
      $arr ['filter_d'] = 0;
  }

  protected function show($var) {
    $show = "";
    if (is_array ( $var ) || is_object ( $var )) {
      $this->printArray((array) $var);
    } elseif (! empty ( $var )) {
      $show ="<div style='border:2px solid #633c04;background-color:#f1dda5;width:300px;height:100;padding: 5px 5px;FONT-SIZE: 7pt; FONT-FAMILY: Verdana;'  >" . $var . "</div>";
      echo $show;
    } else {
      $show = "Empty variable ";
      echo $show;
    }

  }

  public function printArray($a) {

    static $count;
    $count = (isset ( $count )) ? ++ $count : 0;
    $colors = array ('#FFCB72', '#FFB072', '#FFE972', '#F1FF72', '#92FF69', '#6EF6DA', '#72D9FE', '#77FFFF', '#FF77FF' );
    if ($count > count ( $colors )) {
      $count --;
      return;
    }

    if (! is_array ( $a )) {
      echo "Passed argument is not an array!<p>";
      return;
    }

    echo "<table border=1 cellpadding=0 cellspacing=0 bgcolor=$colors[$count]>";

    while ( list ( $k, $v ) = each ( $a ) ) {
      echo "<tr><td style='padding:1em'>$k</td><td style='padding:1em'>$v</td></tr>\n";
      if (is_array ( $v )) {
        echo "<tr><td> </td><td>";
        self::printArray ( $v );
        echo "</td></tr>\n";
      }
    }
    echo "</table>";
    $count --;
  }
}