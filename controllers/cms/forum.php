<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * https://www.banshee-php.org/
	 *
	 * Licensed under The MIT License
	 */

	require_once("../libraries/helpers/output.php");

	class cms_forum_controller extends Banshee\controller {
		private function show_message_overview() {
			if (($message_count = $this->model->count_messages()) === false) {
				$this->view->add_tag("result", "Database error.");
				return false;
			}

			$paging = new Banshee\pagination($this->view, "admin_forum", $this->settings->admin_page_size, $message_count);

			if (($messages = $this->model->get_messages($paging->offset, $paging->size)) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->open_tag("overview");

			$this->view->open_tag("messages");
			foreach ($messages as $message) {
				$message["content"] = truncate_text($message["content"], 400);
				$message["timestamp"] = date("j F Y, H:i", $message["timestamp"]);
				if ($message["author"] == "") {
					$message["author"] = $message["username"];
				}
				$this->view->record($message, "message");
			}
			$this->view->close_tag();

			$paging->show_browse_links();

			$this->view->close_tag();
		}

		private function show_message_form($message) {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				$_SESSION["cms_forum_previous"] = $this->page->previous;
			}

			$this->view->open_tag("edit");
			$this->view->record($message, "message");
			$this->view->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save message") {
					/* Update message
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_message_form($_POST);
					} else if ($this->model->update_message($_POST) === false) {
						$this->view->add_message("Database error while updating message.");
						$this->show_message_form($_POST);
					} else {
						$topic_id = $this->model->get_topic_id($_POST["id"]);
						$this->user->log_action("forum message %d (topic:%d) updated", $_POST["id"], $topic_id);
						if ($_SESSION["cms_forum_previous"] == $this->page->module) {
							$this->show_message_overview();
						} else {
							header("Location: /".$_SESSION["cms_forum_previous"]."#".$_POST["id"]);
						}
						unset($_SESSION["cms_forum_previous"]);
					}
				} else if ($_POST["submit_button"] == "delete") {
					/* Delete message
					 */
					$topic_id = $this->model->get_topic_id($_POST["id"]);

					if ($this->model->delete_message($_POST["message_id"]) == false) {
						$this->view->add_tag("result", "Database error while deleting message.");
					} else {
						$this->user->log_action("forum message %d (topic:%d) deleted", $_POST["message_id"], $topic_id);
						$this->show_message_overview();
					}
				} else {
					$this->show_message_overview();
				}
			} else if (valid_input($this->page->parameters[0], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit existing message
				 */
				if (($message = $this->model->get_message($this->page->parameters[0])) == false) {
					$this->view->add_tag("result", "Message not found.");
				} else {
					$this->show_message_form($message);
				}
			} else {
				/* Show message overview
				 */
				$this->show_message_overview();
			}
		}
	}
?>
