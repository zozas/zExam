					<b>
						[@submit_percentage]%
					</b>
					[@questions_completed]
					<br />
					<b>
						[@questions_unanswered_value]
					</b>
					[@questions_unanswered]
					<br />
					<br />
					<form id='start-form' method='post' class='answer-area'>
						<input type='hidden' name='action' value='[@return-action]' />
						<table>
							<tr>
								<td>
									<button type='submit' id='pin-submit' class='pin-clear'>[@return]</button></td>
								</td>
							</tr>
						</table>
					</form>
					<form id='start-form' method='post' class='answer-area'>
						<input type='hidden' name='action' value='[@end-action]' />
						<table>
							<tr>
								<td>
									<button type='submit' id='pin-submit' class='pin-submit'>[@submit]</button></td>
								</td>
							</tr>
						</table>
					</form>
