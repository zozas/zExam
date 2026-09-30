					<script>
					function validateInput(el) {
						el.value = el.value.replace(/["'\/]/g, "");
					}
					function addQuestion() {
						const container = document.getElementById("questions");
						const index = document.querySelectorAll(".question-block").length + 1;
						const block = document.createElement("div");
						block.className = "question-block";
						block.innerHTML = `<button type="button" class="remove-btn" onclick="removeQuestion(this)"></button>
							[@question] ${index}
							<br />
							<br />
							<label>[@question]</label>
							<br />
							<input type="text" name="q_title[]" placeholder="[@question]" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
							<br />
							<br />
							<label>[@answer] 1</label>
							<br />
							<input type="text" name="a1[]" placeholder="[@answer] 1" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
							<br />
							<br />
							<label>[@answer] 2</label>
							<br />
							<input type="text" name="a2[]" placeholder="[@answer] 2" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
							<br />
							<br />
							<label>[@answer] 3</label>
							<br />
							<input type="text" name="a3[]" placeholder="[@answer] 3" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
							<br />
							<br />
							<label>[@answer] 4</label>
							<br />
							<input type="text" name="a4[]" placeholder="[@answer] 4" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
							<br />
							<br />
							<label>[@question_correct] (1-4):</label>
							<br />
							<input type="number" class="pin-input" name="correct[]" min="1" max="4" required><br><br>`;
						container.appendChild(block);
					}
					function removeQuestion(btn) {
						const block = btn.parentNode;
						block.remove();
						renumberQuestions();
					}
					function renumberQuestions() {
						const blocks = document.querySelectorAll(".question-block");
						blocks.forEach((block, i) => { const h3 = block.querySelector("h3"); h3.textContent = `[@question] ${i + 1}`; });
					}
					</script>
					<form id='pin-form' method='post' class='answer-area'>
						<input type='hidden' name='action' value='admin_generate_file' />
						[@quizdata]
 						<br />
						<br />
						<input type='text' name='pin' placeholder="[@pin]" class="pin-input" required  maxlength='[@pin_length]' pattern='[0-9]{1,[@pin_length]}' oninput="this.value = this.value.replace(/[^0-9]/g, '')">
						<br />
						<br />
						<input type='text' name='admin' placeholder="[@adminpin]" class="pin-input" required maxlength='[@pin_length]' pattern='[0-9]{1,[@pin_length]}' oninput="this.value = this.value.replace(/[^0-9]/g, '')">
						<br />
						<br />
						<input type="text" name="author" placeholder="[@author]" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
						<br />
						<br />
						<input type="text" name="title" placeholder="[@title]" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
						<br />
						<br />
						<input type="text" name="version" placeholder="[@version]" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
						<br />
						<br />
						<input type="text" placeholder="[@instructions]" class="pin-input" pattern="[^\"'\/]+" oninput="validateInput(this)">
						<br />
						<br />
						<input type="text" name="language" placeholder="[@language]" class="pin-input" required pattern="[^\"'\/]+" oninput="validateInput(this)">
						<br />
						<br />
						<h4>[@question_list]</h4>
						<br />
						<br />
						<div id="questions"></div>
						<button type="button" class="admin-btn" onclick="addQuestion()">[@question_add]</button>
						<br />
						<br />
						<button type="submit" class="admin-btn">[@generate]</button>
					</form>
