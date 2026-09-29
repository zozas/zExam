<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	session_start();
	require_once('include.php');
	// Load configuration
	$config = new ini;
	$config->open('config.ini.php');
	$config->read();
	// Load language
	$language = new ini;
	$language->open($config->get('ENCODING', 'LANGUAGE'));
	$language->read();
	// Initialize session and log
	$session = new session;
	// Load template
	$template_main = new template;
	$template_main->open('index.tpl');
	$template_main->set('meta_title', $config->get('APPLICATION', 'TITLE'));
	$template_main->set('meta_viewport', $config->get('APPLICATION', 'VIEWPORT'));
	$template_main->set('meta_charset', $config->get('ENCODING', 'CHARSET'));
	$template_main->set('meta_author', $config->get('APPLICATION', 'AUTHOR'));
	$template_main->set('meta_contact', $config->get('APPLICATION', 'CONTACT'));
	$template_main->set('meta_distribution', $config->get('APPLICATION', 'DISTRIBUTION'));
	$template_main->set('meta_google', $config->get('APPLICATION', 'GOOGLE'));
	$template_main->set('meta_product', $config->get('APPLICATION', 'PRODUCT'));
	$template_main->set('meta_robots', $config->get('APPLICATION', 'ROBOTS'));
	$template_main->set('meta_xua', $config->get('APPLICATION', 'XUA'));
	$template_main->set('meta_type', $config->get('APPLICATION', 'TYPE'));
	$template_main->set('meta_version', $config->get('APPLICATION', 'VERSION'));
	$template_main->set('meta_copyright', $config->get('APPLICATION', 'COPYRIGHT'));
	$template_main->set('meta_description', $config->get('APPLICATION', 'DESCRIPTION'));
	$template_main->set('meta_disclaimer', $config->get('APPLICATION', 'DISCLAIMER'));
	$template_main->set('meta_keywords', $config->get('APPLICATION', 'KEYWORDS'));
	$template_main->set('link_home', $language->get('STRING', 'HOME'));
	$template_main->set('link_about', $language->get('STRING', 'ABOUT'));
	$template_main->set('link_admin', $language->get('STRING', 'ADMIN'));
	$template_main->set('link_sound', $language->get('STRING', 'SOUND'));
	$template_main->set('link_help', $language->get('STRING', 'HELP'));
	// Initialize actions
	$action = '';
	if (isset($_POST['action']))
		$action = $_POST['action'];
	else
		if (isset($_GET['action']))
			$action = $_GET['action'];
	// Interpret actions
	// Admin
	if ($action=='admin') {
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_admin->open('admin.tpl');
		$template_admin->set('create', $language->get('STRING', 'CREATE'));
		$template_admin->set('download', $language->get('STRING', 'DOWNLOAD'));
		$template_admin->set('update', $language->get('STRING', 'UPDATE'));
		$template_admin->set('results', $language->get('STRING', 'RESULTS'));
		$template_admin->set('delete', $language->get('STRING', 'DELETE'));
		$template_admin->set('template', $language->get('STRING', 'TEMPLATE'));
		$template_main->set('game_options', $template_admin->get());
		$template_main->set('game_progress', '');
	// Quiz download
	} else if ($action=='admin_download') {
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_admin->open('download.tpl');
		$template_admin->set('pin_length', $config->get('APPLICATION', 'PIN_LENGTH'));
		$template_admin->set('pin_name', $language->get('STRING', 'PIN_QUIZ'));
		$template_admin->set('admin_pin_name', $language->get('STRING', 'PIN_QUIZ_ADMIN'));
		$template_admin->set('submit', $language->get('STRING', 'DOWNLOAD'));
		$template_admin->set('pin-action', 'admin_download_quiz');
		$template_main->set('game_options', $template_admin->get());
		$template_main->set('game_progress', '');
	// Quiz download file
	} else if ($action=='admin_download_quiz') {
		$pin = '';
		if (isset($_POST['pin']))
			$pin = $_POST['pin'];
		else
			if (isset($_GET['pin']))
				$pin = $_GET['pin'];
		$admin_pin = '';
		if (isset($_POST['admin_pin']))
			$admin_pin = $_POST['admin_pin'];
		else
			if (isset($_GET['admin_pin']))
				$admin_pin = $_GET['admin_pin'];
		$result = '';
		if ($pin === "" || $admin_pin === "") {
			$result = $language->get('STRING', 'ERROR_PIN_EMPTY');
		} else {
			$filepath = $config->get('APPLICATION', 'DATABASES').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
			if (!file_exists($filepath)) {
				$result = $language->get('STRING', 'ERROR_FILE_MISSING');
			} else {
				$content = file_get_contents($filepath);
				if (!$content) {
					$result = $language->get('STRING', 'ERROR_FILE_READ');
				} else {
					$lines = explode("\n", $content);
					if (count($lines) < 5) {
						$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
					} else {
						$iniLines = array_slice($lines, 3, count($lines) - 5);
						$iniContent = implode("\n", $iniLines);
						$ini = parse_ini_string($iniContent, true, INI_SCANNER_RAW);
						if (!$ini || !isset($ini["DATABASE"])) {
							$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
						} else {
							if (!isset($ini["DATABASE"]["ADMIN"])) {
								$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
							} else {
								$db_admin = trim($ini["DATABASE"]["ADMIN"]);
								if ($db_admin !== $admin_pin) {
									$result = $language->get('STRING', 'ERROR_PIN');
								} else {
									if (!file_exists($filepath)) {
										$result = $language->get('STRING', 'ERROR_FILE_MISSING');
									} else {
										$resultspath = $config->get('APPLICATION', 'DATABASES').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
										if (!file_exists($resultspath)) {
											$result = $language->get('STRING', 'ERROR_FILE_MISSING');
										} else {
											header('Content-Description: File Transfer');
											header('Content-Type: application/octet-stream');
											header('Content-Disposition: attachment; filename="'.basename($resultspath).'"');
											header('Expires: 0');
											header('Cache-Control: must-revalidate');
											header('Pragma: public');
											header('Content-Length: '.filesize($resultspath));
											readfile($resultspath);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_main->set('game_options', $language->get('STRING', 'DOWNLOAD'));
		$template_main->set('game_progress', $result);
	// Quiz create
	} else if ($action=='admin_create') {
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_admin->open('create.tpl');
		$template_admin->set('create', $language->get('STRING', 'CREATE'));
		$template_main->set('game_options', $template_admin->get());
		$template_main->set('game_progress', '');
	// Quiz create / upload
	} else if ($action=='admin_create_upload') {
		$result = '';
		if (!isset($_FILES['quiz_upload']) || $_FILES['quiz_upload']['error'] !== UPLOAD_ERR_OK) {
			$result = $language->get('STRING', 'ERROR_FILE_READ');
		} else {
			$tmp = $_FILES['quiz_upload']['tmp_name'];
			$content = file_get_contents($tmp);
			if (!$content) {
				$result = $language->get('STRING', 'ERROR_FILE_READ');
			} else {
				$lines = explode("\n", $content);
				if (count($lines) < 5) {
					$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
				} else {
					$iniLines = array_slice($lines, 3, count($lines) - 5);
					$iniContent = implode("\n", $iniLines);
					$ini = parse_ini_string($iniContent, true, INI_SCANNER_RAW);
					if (!$ini || !isset($ini["DATABASE"])) {
						$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
					} else {
						if (!isset($ini["DATABASE"]["PIN"])) {
							$result = $language->get('STRING', 'ERROR_PIN_MISSING');
						} else {
							$PIN = trim($ini["DATABASE"]["PIN"]);
							if ($PIN === "") {
								$result = $language->get('STRING', 'ERROR_PIN_EMPTY');
							} else {
								$filename = $PIN . ".ini.php";
								$savePath = $config->get('APPLICATION', 'DATABASES').'/'.$filename;
								if (file_exists($savePath)) {
									$result = $language->get('STRING', 'ERROR_PIN_EXISTS');
								} else {
									if (!move_uploaded_file($tmp, $savePath)) {
										$result = $language->get('STRING', 'ERROR_FILE_SAVE');
									} else {
										$result = $language->get('STRING', 'FILE_UPLOAD');
									}
								}
							}
						}
					}
				}
			}
		}
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_main->set('game_options', $language->get('STRING', 'CREATE'));
		$template_main->set('game_progress', $result);
	// Quiz update form
	} else if ($action=='admin_update') {
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_admin->open('update.tpl');
		$template_admin->set('pin_length', $config->get('APPLICATION', 'PIN_LENGTH'));
		$template_admin->set('pin_name', $language->get('STRING', 'PIN_QUIZ'));
		$template_admin->set('admin_pin_name', $language->get('STRING', 'PIN_QUIZ_ADMIN'));
		$template_admin->set('submit', $language->get('STRING', 'UPDATE'));
		$template_admin->set('pin-action', 'admin_update_quiz');
		$template_main->set('game_options', $template_admin->get());
		$template_main->set('game_progress', '');
	// Quiz update
	} else if ($action=='admin_update_quiz') {
		$pin = '';
		if (isset($_POST['pin']))
			$pin = $_POST['pin'];
		else
			if (isset($_GET['pin']))
				$pin = $_GET['pin'];
		$admin_pin = '';
		if (isset($_POST['admin_pin']))
			$admin_pin = $_POST['admin_pin'];
		else
			if (isset($_GET['admin_pin']))
				$admin_pin = $_GET['admin_pin'];
		$result = '';
		if ($pin === "" || $admin_pin === "") {
			$result = $language->get('STRING', 'ERROR_PIN_EMPTY');
		} else {
			$filepath = $config->get('APPLICATION', 'DATABASES').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
			if (!file_exists($filepath)) {
				$result = $language->get('STRING', 'ERROR_FILE_MISSING');
			} else {
				$content = file_get_contents($filepath);
				if (!$content) {
					$result = $language->get('STRING', 'ERROR_FILE_READ');
				} else {
					$lines = explode("\n", $content);
					if (count($lines) < 5) {
						$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
					} else {
						$iniLines = array_slice($lines, 3, count($lines) - 5);
						$iniContent = implode("\n", $iniLines);
						$ini = parse_ini_string($iniContent, true, INI_SCANNER_RAW);
						if (!$ini || !isset($ini["DATABASE"])) {
							$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
						} else {
							if (!isset($ini["DATABASE"]["ADMIN"])) {
								$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
							} else {
								$db_admin = trim($ini["DATABASE"]["ADMIN"]);
								if ($db_admin !== $admin_pin) {
									$result = $language->get('STRING', 'ERROR_PIN');
								} else {
									if (!isset($_FILES['quiz_upload']) || $_FILES['quiz_upload']['error'] !== UPLOAD_ERR_OK) {
										$result = $language->get('STRING', 'ERROR_FILE_READ');
									} else {
										$tmp = $_FILES['quiz_upload']['tmp_name'];
										$content = file_get_contents($tmp);
										if (!$content) {
											$result = $language->get('STRING', 'ERROR_FILE_READ');
										} else {
											$lines = explode("\n", $content);
											if (count($lines) < 5) {
												$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
											} else {
												$iniLines = array_slice($lines, 3, count($lines) - 5);
												$iniContent = implode("\n", $iniLines);
												$ini = parse_ini_string($iniContent, true, INI_SCANNER_RAW);
												if (!$ini || !isset($ini["DATABASE"])) {
													$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
												} else {
													if (!isset($ini["DATABASE"]["PIN"])) {
														$result = $language->get('STRING', 'ERROR_PIN_MISSING');
													} else {
														$new_pin = trim($ini["DATABASE"]["PIN"]);
														if ($new_pin === "") {
															$result = $language->get('STRING', 'ERROR_PIN_EMPTY');
														} else {
															$filename = $new_pin . ".ini.php";
															$savePath = $config->get('APPLICATION', 'DATABASES').'/'.$filename;
															if ($new_pin != $pin) {
																$result = $language->get('STRING', 'ERROR_PIN_DIFFERENT');
															} else {
																if (!unlink($savePath)) {
																	$result = $language->get('STRING', 'ERROR_FILE_DELETE');
																} else {
																	if (!move_uploaded_file($tmp, $savePath)) {
																		$result = $language->get('STRING', 'ERROR_FILE_SAVE');
																	} else {
																		$result = $language->get('STRING', 'FILE_UPDATE');
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_main->set('game_options', $language->get('STRING', 'UPDATE'));
		$template_main->set('game_progress', $result);
	// Quiz results form
	} else if ($action=='admin_results') {
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_admin->open('results.tpl');
		$template_admin->set('pin_length', $config->get('APPLICATION', 'PIN_LENGTH'));
		$template_admin->set('pin_name', $language->get('STRING', 'PIN_QUIZ'));
		$template_admin->set('admin_pin_name', $language->get('STRING', 'PIN_QUIZ_ADMIN'));
		$template_admin->set('submit', $language->get('STRING', 'RESULTS'));
		$template_admin->set('pin-action', 'admin_results_quiz');
		$template_main->set('game_options', $template_admin->get());
		$template_main->set('game_progress', '');
	// Quiz results
	} else if ($action=='admin_results_quiz') {
		$pin = '';
		if (isset($_POST['pin']))
			$pin = $_POST['pin'];
		else
			if (isset($_GET['pin']))
				$pin = $_GET['pin'];
		$admin_pin = '';
		if (isset($_POST['admin_pin']))
			$admin_pin = $_POST['admin_pin'];
		else
			if (isset($_GET['admin_pin']))
				$admin_pin = $_GET['admin_pin'];
		$result = '';
		if ($pin === "" || $admin_pin === "") {
			$result = $language->get('STRING', 'ERROR_PIN_EMPTY');
		} else {
			$filepath = $config->get('APPLICATION', 'DATABASES').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
			if (!file_exists($filepath)) {
				$result = $language->get('STRING', 'ERROR_FILE_MISSING');
			} else {
				$content = file_get_contents($filepath);
				if (!$content) {
					$result = $language->get('STRING', 'ERROR_FILE_READ');
				} else {
					$lines = explode("\n", $content);
					if (count($lines) < 5) {
						$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
					} else {
						$iniLines = array_slice($lines, 3, count($lines) - 5);
						$iniContent = implode("\n", $iniLines);
						$ini = parse_ini_string($iniContent, true, INI_SCANNER_RAW);
						if (!$ini || !isset($ini["DATABASE"])) {
							$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
						} else {
							if (!isset($ini["DATABASE"]["ADMIN"])) {
								$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
							} else {
								$db_admin = trim($ini["DATABASE"]["ADMIN"]);
								if ($db_admin !== $admin_pin) {
									$result = $language->get('STRING', 'ERROR_PIN');
								} else {
									$resultspath = $config->get('APPLICATION', 'RESULTS').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
									if (!file_exists($resultspath)) {
										$result = $language->get('STRING', 'ERROR_FILE_MISSING');
									} else {
										echo "\xEF\xBB\xBF";
										$headers = [$language->get('STRING', 'PIN_ID'), $language->get('STRING', 'PIN_QUIZ'), $language->get('STRING', 'RESULTS'), $language->get('STRING', 'SUBMIT_TIME'), $language->get('STRING', 'IP')];
										$lines = file($resultspath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
										if (!$lines) {
											$result = $language->get('STRING', 'ERROR_FILE_MISSING');
										} else {
											$fp = fopen('php://memory', 'w');
											fputcsv($fp, $headers, ',', '"', '\\');
											foreach ($lines as $line) {
												$parts = explode(',', $line);
												$parts = array_map('trim', $parts);
												fputcsv($fp, $parts);
											}
											rewind($fp);
											header('Content-Type: text/csv');
											header('Content-Disposition: attachment; filename="data_export.csv"');
											header('Pragma: no-cache');
											header('Expires: 0');
											fpassthru($fp);
											exit();
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_main->set('game_options', $language->get('STRING', 'RESULTS'));
		$template_main->set('game_progress', $result);
	// Delete quiz form
	} else if ($action=='admin_delete') {
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_admin->open('delete.tpl');
		$template_admin->set('pin_length', $config->get('APPLICATION', 'PIN_LENGTH'));
		$template_admin->set('pin_name', $language->get('STRING', 'PIN_QUIZ'));
		$template_admin->set('admin_pin_name', $language->get('STRING', 'PIN_QUIZ_ADMIN'));
		$template_admin->set('submit', $language->get('STRING', 'DELETE'));
		$template_admin->set('pin-action', 'admin_delete_quiz');
		$template_main->set('game_options', $template_admin->get());
		$template_main->set('game_progress', '');
	// Delete quiz
	} else if ($action=='admin_delete_quiz') {
		$pin = '';
		if (isset($_POST['pin']))
			$pin = $_POST['pin'];
		else
			if (isset($_GET['pin']))
				$pin = $_GET['pin'];
		$admin_pin = '';
		if (isset($_POST['admin_pin']))
			$admin_pin = $_POST['admin_pin'];
		else
			if (isset($_GET['admin_pin']))
				$admin_pin = $_GET['admin_pin'];
		$result = '';
		if ($pin === "" || $admin_pin === "") {
			$result = $language->get('STRING', 'ERROR_PIN_EMPTY');
		} else {
			$filepath = $config->get('APPLICATION', 'DATABASES').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
			if (!file_exists($filepath)) {
				$result = $language->get('STRING', 'ERROR_FILE_MISSING');
			} else {
				$content = file_get_contents($filepath);
				if (!$content) {
					$result = $language->get('STRING', 'ERROR_FILE_READ');
				} else {
					$lines = explode("\n", $content);
					if (count($lines) < 5) {
						$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
					} else {
						$iniLines = array_slice($lines, 3, count($lines) - 5);
						$iniContent = implode("\n", $iniLines);
						$ini = parse_ini_string($iniContent, true, INI_SCANNER_RAW);
						if (!$ini || !isset($ini["DATABASE"])) {
							$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
						} else {
							if (!isset($ini["DATABASE"]["ADMIN"])) {
								$result = $language->get('STRING', 'ERROR_FILE_STRUCTURE');
							} else {
								$db_admin = trim($ini["DATABASE"]["ADMIN"]);
								if ($db_admin !== $admin_pin) {
									$result = $language->get('STRING', 'ERROR_PIN');
								} else {
									if (!unlink($filepath)) {
										$result = $language->get('STRING', 'ERROR_FILE_DELETE');
									} else {
										$resultspath = $config->get('APPLICATION', 'RESULTS').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
										if (!unlink($resultspath)) {
											$result = $language->get('STRING', 'ERROR_FILE_DELETE');
										} else {
											$result = $language->get('STRING', 'FILE_DELETED');
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$template_main->set('game_content', $language->get('STRING', 'ADMIN'));
		$template_admin = new template;
		$template_main->set('game_options', $language->get('STRING', 'DELETE'));
		$template_main->set('game_progress', $result);
	// Download quiz template
	} else if ($action=='admin_template') {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($config->get('TEMPLATES', 'DATABASE')).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($config->get('TEMPLATES', 'DATABASE')));
		readfile($config->get('TEMPLATES', 'DATABASE'));
	// About
	} else if ($action=='about') {
		$template_main->set('game_content', $language->get('STRING', 'ABOUT'));
		$template_about = new template;
		$template_about->open('about.tpl');
		$template_about->set('title', $config->get('APPLICATION', 'TITLE'));
		$template_about->set('version', $config->get('APPLICATION', 'VERSION'));
		$template_about->set('app_version', $language->get('STRING', 'VERSION'));
		$template_about->set('author', $config->get('APPLICATION', 'AUTHOR'));
		$template_about->set('contact', $config->get('APPLICATION', 'CONTACT'));
		$template_about->set('copyright', $config->get('APPLICATION', 'COPYRIGHT'));
		$template_about->set('description', $config->get('APPLICATION', 'DESCRIPTION'));
		$template_about->set('disclaimer', $config->get('APPLICATION', 'DISCLAIMER'));
		$template_about->set('databases', $language->get('STRING', 'DATABASES'));
		$template_about->set('total_databases', count(glob($config->get('APPLICATION', 'DATABASES').'/*')));
		$template_main->set('game_options', $template_about->get());
		$template_main->set('game_progress', '');
	} else if ($action=='submit') {
		if($session->exist('PIN') && $session->exist('ID') && $session->exist('TOTAL') && $session->exist('CURRENT') && $session->exist('QUESTIONS') && $session->exist('ANSWERS')) {
			$database_file = $config->get('APPLICATION', 'DATABASES').'/'.$session->get('PIN').'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
			if (file_exists($database_file)) {		
				$score = 0;
				for ($i = 1; $i <= $session->get('TOTAL'); $i = $i + 1) {
					if ($session->get('ANSWERS')[$i] == $session->get('QUESTIONS')[$i-1]['correct']) {
						$score = $score + 1;
					}
				}
				$database = new ini;
				$database->open($database_file);
				$database->read();
				$template_main->set('game_content', $language->get('STRING', 'RESULTS'));
				$template_results = new template;
				$template_results->open('quiz.tpl');
				$template_results->set('pin_quiz', $database->get('DATABASE', 'PIN'));
				$template_results->set('title', $database->get('DATABASE', 'TITLE'));
				$template_results->set('quiz', $language->get('STRING', 'PIN_QUIZ'));
				$template_results->set('pin', $language->get('STRING', 'PIN_ID'));
				$template_results->set('max_grade', $config->get('APPLICATION', 'GRADE'));
				$template_results->set('pin_id', $session->get('ID'));
				$template_results->set('result_grade', number_format(floatval($score/$database->get('DATABASE', 'QUESTIONS'))*$config->get('APPLICATION', 'GRADE'),2));
				$template_main->set('game_options', $template_results->get());
				$template_main->set('game_progress', '');
				$result_file = $config->get('APPLICATION', 'RESULTS').'/'.$session->get('PIN').'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
				file_put_contents($result_file, $session->get('ID').','.$database->get('DATABASE', 'PIN').','.number_format(floatval($score/$database->get('DATABASE', 'QUESTIONS'))*$config->get('APPLICATION', 'GRADE'),2).','.date('Y-m-d H:i:s').','.$session->ip().PHP_EOL, FILE_APPEND);
			} else {
				$session->erase_session();
				$template_redirect = new template;
				$template_redirect->open('redirect.tpl');
				$template_redirect->set('url', '?');
				$template_main->set('game_content', '');
				$template_main->set('game_options', '');
				$template_main->set('game_progress', $template_redirect->get());
			}
		} else {
			$session->erase_session();
			$template_redirect = new template;
			$template_redirect->open('redirect.tpl');
			$template_redirect->set('url', '?');
			$template_main->set('game_content', '');
			$template_main->set('game_options', '');
			$template_main->set('game_progress', $template_redirect->get());
		}
	// Exam questions
	} else if ($action=='exam') {
		$current = '1';
		if (isset($_POST['current']))
			$current = $_POST['current'];
		else
			if (isset($_GET['current']))
				$current = $_GET['current'];
		if ($session->exist('TOTAL') && $session->exist('CURRENT')) {
			$current_question_index = intVal($session->get('CURRENT')) + intVal($current);
			if ($current_question_index <= 0) {
				$current_question_index = 0;
				$current = 0;
			}
			if ($current_question_index > $session->get('TOTAL')) {
				$current_question_index = $session->get('TOTAL') - 1;
				$current = 0;
			}			
			$session->set('CURRENT', $current_question_index);
		} else {
			$session->erase_session();
			$template_redirect = new template;
			$template_redirect->open('redirect.tpl');
			$template_redirect->set('url', '?');
			$template_main->set('game_content', '');
			$template_main->set('game_options', '');
			$template_main->set('game_progress', $template_redirect->get());
		}
		$answer = '';
		if (isset($_POST['answer']))
			$answer = $_POST['answer'];
		else
			if (isset($_GET['answer']))
				$answer = $_GET['answer'];
		$answers = $session->get('ANSWERS');
		if ($answer!='') {
			if (isset($answers[$session->get('CURRENT')])) {
				if ($answers[$session->get('CURRENT')] != $answer) {
					$answers[$session->get('CURRENT')] = $answer;
				}
			} else {
				$answers[$session->get('CURRENT')] = $answer;
			}
			$session->set('ANSWERS', $answers);
		}
		if($session->exist('PIN') && $session->exist('ID') && $session->exist('TOTAL') && $session->exist('CURRENT') && $session->exist('QUESTIONS') && $session->exist('ANSWERS')) {
			if ($session->get('CURRENT') < $session->get('TOTAL')) {
				$template_question = new template;
				$template_question->open('question.tpl');
				$template_main->set('game_content', $session->get('QUESTIONS')[$session->get('CURRENT')]['question']);
				$template_question->set('question_text', $session->get('QUESTIONS')[$session->get('CURRENT')]['question']);
				$suffled_answers = range(1, 4);
				shuffle($suffled_answers);
				for ($i = 0; $i <4; $i++) {
					if ($answers[$session->get('CURRENT')+1] == $session->get('QUESTIONS')[$session->get('CURRENT')]['answer'][$suffled_answers[$i]]) {
						$template_question->set('question_option_'.($i+1), $session->get('QUESTIONS')[$session->get('CURRENT')]['answer'][$suffled_answers[$i]]);
						$template_question->set('status', 1);
					} else {
						$template_question->set('question_option_'.($i+1), $session->get('QUESTIONS')[$session->get('CURRENT')]['answer'][$suffled_answers[$i]]);
						$template_question->set('status', 0);
					}
				}
				$template_main->set('game_options', $template_question->get());
				$template_progress = new template;
				$template_progress->open('progress.tpl');
				$template_progress->set('current_question', ($session->get('CURRENT')+1));
				$template_progress->set('from', $language->get('STRING', 'FROM'));
				$template_progress->set('total_questions', $session->get('TOTAL'));
				$template_main->set('game_progress', $template_progress->get());
			} else {
				$template_end = new template;
				$template_end->open('end.tpl');
				$template_end->set('submit', $language->get('STRING', 'SUBMIT'));
				$template_end->set('end-action', 'submit');
				$template_end->set('return-action', 'exam');
				$template_main->set('game_content', $language->get('STRING', 'SUBMIT'));
				$unanswered_questions = 0;
				for ($i = 1; $i <= $session->get('TOTAL'); $i = $i + 1) {
					if ($session->get('ANSWERS')[$i] == '')
						$unanswered_questions = $unanswered_questions + 1;
				}
				$template_end->set('questions_unanswered', $language->get('STRING', 'QUESTIONS_UNANSWERED'));
				$template_end->set('questions_completed', $language->get('STRING', 'QUESTIONS_COMPLETED'));
				$template_end->set('questions_unanswered_value', $unanswered_questions.' / '.$session->get('TOTAL'));
				$template_end->set('submit_percentage', number_format(($session->get('TOTAL')-$unanswered_questions)/$session->get('TOTAL')*100, 2));	
				$template_end->set('return', $language->get('STRING', 'RETURN'));
				$template_main->set('game_options', $language->get('STRING', 'SUBMIT_QUESTION'));
				$template_main->set('game_progress', $template_end->get());
			}
		} else {
			$session->erase_session();
			$template_redirect = new template;
			$template_redirect->open('redirect.tpl');
			$template_redirect->set('url', '?');
			$template_main->set('game_content', '');
			$template_main->set('game_options', '');
			$template_main->set('game_progress', $template_redirect->get());
		}
	// Start exam
	} else if ($action=='start') {
		$id = '';
		if (isset($_POST['id']))
			$id = $_POST['id'];
		else
			if (isset($_GET['id']))
				$id = $_GET['id'];
		if ($id != '' && $session->exist('PIN')) {
			$database_file = $config->get('APPLICATION', 'DATABASES').'/'.$session->get('PIN').'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
			if (file_exists($database_file)) {
				$session->set('ID', $id);
				$database = new ini;
				$database->open($database_file);
				$database->read();
				if ($session->get('PIN') == $database->get('DATABASE', 'PIN')) {
					$questions = [];
					for ($i = 0; $i < $database->get('DATABASE', 'QUESTIONS'); $i++) {
						$questions[$i]['id'] = random_int(1, $database->get('DATABASE', 'ENTRIES'));
						$questions[$i]['question'] = $database->get('Q_'.$questions[$i]['id'], 'TITLE');
						$questions[$i]['answer'][1] = $database->get('Q_'.$questions[$i]['id'], 'ANSWER_1');
						$questions[$i]['answer'][2] = $database->get('Q_'.$questions[$i]['id'], 'ANSWER_2');
						$questions[$i]['answer'][3] = $database->get('Q_'.$questions[$i]['id'], 'ANSWER_3');
						$questions[$i]['answer'][4] = $database->get('Q_'.$questions[$i]['id'], 'ANSWER_4');
						$questions[$i]['correct'] = $database->get('Q_'.$questions[$i]['id'], 'ANSWER_'.$database->get('Q_'.$questions[$i]['id'], 'CORRECT'));
					}
					$session->set('QUESTIONS', $questions);
					$answers = [];
					for ($i = 0; $i <= $database->get('DATABASE', 'QUESTIONS'); $i++) {
						$answers[$i] = '';
					}
					$session->set('ANSWERS', $answers);
					$session->set('CURRENT', 0);
					$session->set('TOTAL', $database->get('DATABASE', 'QUESTIONS'));
					$template_start = new template;
					$template_start->open('start.tpl');
					$template_start->set('start', $language->get('STRING', 'START'));
					$template_start->set('start-current', $session->get('CURRENT'));
					$template_start->set('start-action', 'exam');
					$template_main->set('game_content', $database->get('DATABASE', 'TITLE'));
					$template_main->set('game_options', $database->get('DATABASE', 'INSTRUCTIONS'));
					$template_main->set('game_progress', $template_start->get());
				} else {
					$session->erase_session();
					$template_redirect = new template;
					$template_redirect->open('redirect.tpl');
					$template_redirect->set('url', '?');
					$template_main->set('game_content', '');
					$template_main->set('game_options', '');
					$template_main->set('game_progress', $template_redirect->get());
				}
			} else {
				$session->erase_session();
				$template_redirect = new template;
				$template_redirect->open('redirect.tpl');
				$template_redirect->set('url', '?');
				$template_main->set('game_content', '');
				$template_main->set('game_options', '');
				$template_main->set('game_progress', $template_redirect->get());
			}
		} else {
			$session->erase_session();
			$template_redirect = new template;
			$template_redirect->open('redirect.tpl');
			$template_redirect->set('url', '?');
			$template_main->set('game_content', '');
			$template_main->set('game_options', '');
			$template_main->set('game_progress', $template_redirect->get());
		}
	// Verify ID or not before starting
	} else if ($action=='id') {
		$pin = '';
		if (isset($_POST['pin']))
			$pin = $_POST['pin'];
		else
			if (isset($_GET['pin']))
				$pin = $_GET['pin'];
		$database_file = $config->get('APPLICATION', 'DATABASES').'/'.$pin.'.'.$config->get('APPLICATION', 'DATABASE_EXTENSION');
		if (file_exists($database_file)) {
			$database = new ini;
			$database->open($database_file);
			$database->read();
			if ($pin == $database->get('DATABASE', 'PIN')) {
				$session->set('PIN', $pin);
				$template_pinpad = new template;
				$template_pinpad->open('pinpad.tpl');
				$template_pinpad->set('pin_length', $config->get('APPLICATION', 'PIN_LENGTH'));
				$template_pinpad->set('pin_name', $language->get('STRING', 'PIN_ID'));
				$template_pinpad->set('pin-action', 'start');
				$template_pinpad->set('pin_variable', 'id');
				$template_main->set('game_content', $language->get('STRING', 'PIN_ID_HELP'));
				$template_main->set('game_options', '');
				$template_main->set('game_progress', $template_pinpad->get());
			} else {
				$session->erase_session();
				$template_redirect = new template;
				$template_redirect->open('redirect.tpl');
				$template_redirect->set('url', '?');
				$template_main->set('game_content', '');
				$template_main->set('game_options', '');
				$template_main->set('game_progress', $template_redirect->get());
			}
		} else {
			$session->erase_session();
			$template_redirect = new template;
			$template_redirect->open('redirect.tpl');
			$template_redirect->set('url', '?');
			$template_main->set('game_content', '');
			$template_main->set('game_options', '');
			$template_main->set('game_progress', $template_redirect->get());
		}
	// Default start menu
	} else {
		$template_pinpad = new template;
		$template_pinpad->open('pinpad.tpl');
		$template_pinpad->set('pin_length', $config->get('APPLICATION', 'PIN_LENGTH'));
		$template_pinpad->set('pin_name', $language->get('STRING', 'PIN_QUIZ'));
		$template_pinpad->set('pin-action', 'id');
		$template_pinpad->set('pin_variable', 'pin');
		$template_main->set('game_content', $language->get('STRING', 'PIN_QUIZ_HELP'));
		$template_main->set('game_options', '');
		$template_main->set('game_progress', $template_pinpad->get());
	}
	echo $template_main->get();
?>
