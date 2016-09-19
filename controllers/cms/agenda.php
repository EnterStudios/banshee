<?php
	class cms_agenda_controller extends controller {
		public function show_agenda_overview() {
			if (($appointments = $this->model->get_appointments()) === false) {
				$this->output->add_tag("result", "Database error");
				return;
			}

			$this->output->open_tag("overview");

			$this->output->open_tag("appointments", array("now" => strtotime("yesterday 23:59:59")));
			foreach ($appointments as $appointment) {
				if ($this->output->mobile == false) {
					$appointment["begin"] = date("l, j F Y", strtotime($appointment["begin"]));
					$appointment["end"] = date("l, j F Y", strtotime($appointment["end"]));
				}
				$appointment["timestamp"] = strtotime($appointment["begin"]." 00:00:00");
				$this->output->record($appointment, "appointment");
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function show_appointment_form($appointment) {
			$this->output->add_javascript("jquery/jquery-ui.js");
			$this->output->add_javascript("banshee/datepicker.js");
			$this->output->add_ckeditor("div.btn-group");

			$this->output->add_css("jquery/jquery-ui.css");

			$this->output->record($appointment, "edit");
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save appointment") {
					/* Save appointment
					 */
					if ($this->model->appointment_oke($_POST) == false) {
						$this->show_appointment_form($_POST);
					} else if (isset($_POST["id"]) == false) {
						/* Create appointment
						 */
						if ($this->model->create_appointment($_POST) == false) {
							$this->output->add_message("Error while creating appointment.");
							$this->show_appointment_form($_POST);
						} else {
							$this->user->log_action("appointment %d created", $db->last_insert_id);
							$this->show_agenda_overview();
						}
					} else {
						/* Update appointment
						 */
						if ($this->model->update_appointment($_POST) == false) {
							$this->output->add_message("Error while updating appointment.");
							$this->show_appointment_form($_POST);
						} else {
							$this->user->log_action("appointment %d updated", $_POST["id"]);
							$this->show_agenda_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete appointment") {
					/* Delete appointment
					 */
					if ($this->model->delete_appointment($_POST["id"]) == false) {
						$this->output->add_tag("result", "Error while deleting appointment.");
					} else {
						$this->user->log_action("appointment %d deleted", $_POST["id"]);
						$this->show_agenda_overview();
					}
				} else {
					$this->show_agenda_overview();
				}
			} else if ($this->page->pathinfo[2] == "new") {
				/* New appointment
				 */
				$appointment = array(
					"begin" => date("Y-m-d"),
					"end"   => date("Y-m-d"));
				$this->show_appointment_form($appointment);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit appointment
				 */
				if (($appointment = $this->model->get_appointment($this->page->pathinfo[2])) == false) {
					$this->output->add_tag("result", "Agendapunten niet gevonden.");
				} else {
					$this->show_appointment_form($appointment);
				}
			} else {
				/* Show month
				 */
				$this->show_agenda_overview();
			}
		}
	}
?>
