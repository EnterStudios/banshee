<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * https://www.banshee-php.org/
	 *
	 * Licensed under The MIT License
	 */

	namespace Banshee;

	class newsletter extends Protocols\email {
		private $footers = array();

		/* Constructor
		 *
		 * INPUT:  string subject[, string e-mail][, string name]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($subject, $from_address = null, $from_name = null) {
			$subject = utf8_decode($subject);

			array_push($this->footers, "Banshee website: <a href=\"http://".$_SERVER["SERVER_NAME"]."/\">".$_SERVER["SERVER_NAME"]."</a>");
			array_push($this->footers, "To unsubscribe from this newsletter, click <a href=\"http://".$_SERVER["SERVER_NAME"]."/newsletter\">here</a>.");
			parent::__construct($subject, $from_address, $from_name);
		}

		/* Add e-mail footer
		 *
		 * INPUT:  string footer
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_footer($str) {
			array_push($this->footers, $str);
		}

		/* Set newsletter content
		 *
		 * INPUT:  string content
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function message($content) {
			$content = utf8_decode($content);

			$content = str_replace("\n\n", "</p>\n<p>", $content);
			$content = str_replace("\n", "<br>\n", $content);

			$footer = implode("<span style=\"margin:0 10px\">|</span>", $this->footers);

			$message = file_get_contents("../extra/newsletter.txt");
			$this->set_message_fields(array(
				"TITLE"   => $this->subject,
				"CONTENT" => $content,
				"FOOTER"  => $footer));

			parent::message($message);
		}
	}
?>
