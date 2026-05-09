<?php

/*
 Description
 * @author     tkbc.com
 * @date       25.7.2009
 * @copyright  TKBC
 * @version    1.0
 * @link       http://tkbc.cz


 */


class Models_Training{

	/**
	 * data
	 * @access public
	 * @var array
	 */
	private static $data;


	/**
	 * vraci data
	 * @return void
	 */
	public static function getData(){

		self::data();

		return self::$data;

	}

	/**
	 * pole treninku
	 * @return void
	 */
	private static function data(){

		//dostál box po-17:30-19:00, st- 17:30-19:00, pa 17:30-19:00, eva thai-kick: ut- 19:00-20:30, ct- 19:00- 20:30 


		self::$data = array(
		 
      '10' => array(

        'PO' => array(

             'mma'=>array(

		array('MMA','10 - 12:00','Píno','pino','Kníže','knize')
		 
		)
		),

       'UT' => array(),
       'ST' => array(

             'mma'=>array(

		array('MMA','10 - 12:00','Píno','pino','Kníže','knize')
		 
		)
		),
       'CT' => array(

		 
		),
       'PA' => array(

             'mma'=>array(

		array('MMA','10 - 12:00','Píno','pino','Kníže','knize')
		 
		)
		),

       'SO' => array(

		 
		),
        'NE' => array(

		 
		)
		),

      '17' => array(

        'PO' => array(

            'box'=>array(

		array('Box','17:30-19:00','Pavel','pavel')
		 
		)
		 
		),

       'UT' => array(

		 
		),
       'ST' => array(
        'box'=>array(

		array('Box','17:30-19:00','Pavel','pavel')
		 
		)
		 
		),
       'CT' => array(


		),
       'PA' => array(
        'box'=>array(

		array('Box','17:30-19:00','Pavel','pavel')
		 
		)
		 
		),

       'SO' => array(


		),
        'NE' => array(
		 
            'mma'=>array(

		array('MMA','17 - 19:00','Píno','pino','Kníže','knize')
		 
		)
		 
		)
		),

     '18' => array(

        'PO' => array(

		 
		),

       'UT' => array(

             'mma'=>array(

		array('MMA','18:00 - 20:00','Píno','pino','Kníže','knize')
		 
		)
		),
       'ST' => array(

		 
		),
       'CT' => array(

             'mma'=>array(

		array('MMA','18:00 - 20:00','Píno','pino','Kníže','knize')
		 
		)
		),
       'PA' => array(

		 
		),

       'SO' => array(


		),
        'NE' => array(

		 
		)
		),
      '19' => array(

        'PO' => array(

             'thai'=>array(

		array('Thai box','19.00-20.30','Zdenda','zdenda')
		 
		)
		 
		),

       'UT' => array(

              'kickbox'=>array(

		array('Thai kick','19:00-20:30','Eva','eva')
		 
		)
		),
       'ST' => array(
          'thai'=>array(

		array('Thai box','19.00-20.30','Zdenda','zdenda')
		 
		)
		 
		 
		),
       'CT' => array(

		 
             'kickbox'=>array(

		array('Thai kick','19:00-20:30','Eva','eva')
		 
		)
		),
       'PA' => array(
          'thai'=>array(

		array('Thai box','19.00-20.30','Zdenda','zdenda')
		 
		)
		 
		),

       'SO' => array(


		),
        'NE' => array(
          'thai'=>array(

		array('Thai box','19.00-20.30','Zdenda','zdenda')
		 
		)
		 
		)
		)
		);
		 
		 

	}


}