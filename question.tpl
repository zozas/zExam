					<form method='post' class='answer-area'>
						<div class='answer-grid'>
							<input type='hidden' name='action' value='exam' />
							<input type='hidden' name='current' value='1' />
							<script>
								if (!isMuted) {
									const text = "[@question_text] : [@question_option_1], [@question_option_2], [@question_option_3], [@question_option_4]";
									const utterance = new SpeechSynthesisUtterance(text);
									utterance.lang = 'el-GR';
									utterance.pitch = 1;
									utterance.rate = 1;
									utterance.volume = 1;
									speechSynthesis.speak(utterance);
								}
							</script>
							<button class='answer-btn' name='answer' value='[@question_option_1]' data-symbol='&#x2680;' style='background: #B71C1C'>
								[@question_option_1]
							</button>
							<button class='answer-btn' name='answer' value='[@question_option_2]' data-symbol='&#x2681;' style='background: #1B5E20'>
								[@question_option_2]
							</button>
							<button class='answer-btn' name='answer' value='[@question_option_3]' data-symbol='&#x2682;' style='background: #0D47A1'>
								[@question_option_3]
							</button>
							<button class='answer-btn' name='answer' value='[@question_option_4]' data-symbol='&#x2683;' style='background: #F9A825'>
								[@question_option_4]
							</button>
						</div>
					</form>
					<h2>
						<div id='status-box'>...</div>
					</h2>
					<script>
						let statusValue = [@status];
						if (statusValue === 0) {
							document.getElementById('status-box').innerHTML = '&#8987;';
						} else {
							document.getElementById('status-box').innerHTML = '&#9989;';
						}
					</script>
