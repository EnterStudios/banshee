<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * https://www.banshee-php.org/
	 *
	 * Licensed under The MIT License
	 */

	class cms_organisation_model extends Banshee\tablemanager_model {
		protected $table = "organisations";
		protected $elements = array(
			"name" => array(
				"label"    => "Name",
				"type"     => "varchar",
				"overview" => true,
				"required" => true,
				"unique"   => true));

		public function get_users($organisation_id) {
			$query = "select * from users where organisation_id=%d order by fullname";

			return $this->db->execute($query, $organisation_id);
		}

		public function delete_oke($item_id) {
			if (parent::delete_oke($item_id) == false) {
				return false;
			}

			$query = "select count(*) as count from users where organisation_id=%d";

			if (($result = $this->db->execute($query, $item_id)) === false) {
				$this->view->add_system_warming("Database error.");
				return false;
			}

			if ((int)$result[0]["count"] > 0) {
				$this->view->add_message("Organisation in use.");
				return false;
			}

			return true;
		}
	}
?>
