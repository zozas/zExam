- Working deployment : <b>https://zexam.xo.je</b>
- Configuration file : config.ini.php
- Language file for translations : language.ini.php (all strings stored) and help.html (manual)
- 2 directories with full read/write premissions named "data" (the uploaded quiz databases) and "results" (the results stored), as defined in the config.ini.php file
- Ready to use

<h1>Manual</h1>

<h2>1. Overview & Application Architecture</h2>
<p>
    The application is designed for creating, managing, and conducting electronic questionnaires/tests.
    It is built on PHP architecture and uses flat-file databases.
</p>

<h2>2. Philosophy</h2>
<ul>
    <li>Access for participants is performed from the main page using (a) the PIN and (b) the student ID number.</li>
    <li>Quizzes are completed quickly.</li>
    <li>Each user can complete the same quiz multiple times.</li>
    <li>Each quiz consists of multiple-choice questions with four possible answers.</li>
    <li>Each quiz is designed to include a specific number of questions selected from the total question pool stored in the database.</li>
    <li>The selection of questions is random.</li>
</ul>

<h2>3. Participation in a Quiz</h2>
<p>
    Each quiz has a unique PIN and is stored as a unique file in the application's database.
    Participants may answer a quiz multiple times, and the score is stored for each attempt.
    In each attempt, the participant may leave some questions unanswered.
    Each attempt stores the score, completion time, and IP address.
</p>

<h2>4. Management</h2>
<p>
    Each quiz, apart from the PIN, also has an admin PIN.
    The admin PIN is the unique management code of the database and is used for verification before deleting a quiz,
    verification before editing, and protection from unauthorized access.
    Access to the management environment is done through the control panel, and the following functions are available:
</p>
<ul>
    <li>New Quiz Creation, where the administrator uploads a quiz file.</li>
    <li>Update Existing Quiz, where the administrator uploads a new quiz file to replace the existing one.
        The quiz PIN must remain the same, while the admin PIN may be different.</li>
    <li>Results, where quiz results are returned as a CSV file.</li>
    <li>Delete Quiz, where the quiz and all results are deleted using both PINs.</li>
    <li>Template Design, where a quiz template file is returned in PHP format for editing.</li>
</ul>

<h2>5. Quiz Design Template</h2>
<p>
    The template file for creating quizzes contains the following fields, and its structure must be strictly identical
    for every quiz uploaded to the application. Fields must be exactly as defined.
    Strings must be enclosed in quotes, e.g., "Question 1", and numbers may be without quotes, e.g., CORRECT = 2.
    Empty questions are not allowed.
</p>

<p>
    Each quiz is an INI file wrapped inside a PHP wrapper for security. It is stored as:
</p>
<pre>
PIN.ini.php
</pre>

<p>The file contains one <strong>DATABASE</strong> section and multiple <strong>Q_x</strong> sections (one for each question).</p>

<p>Each quiz file is protected by a PHP wrapper and must begin with:</p>
<pre>
;<?php
;die();
;/*
</pre>

<p>and must end with:</p>
<pre>
*/
;?>
</pre>

<h2>6. DATABASE Section in the Template</h2>
<p>
    The [DATABASE] section contains the basic quiz information:
</p>
<ul>
    <li><strong>ADMIN</strong>: The admin PIN, which must not be disclosed to anyone.</li>
    <li><strong>AUTHOR</strong>: The name of the quiz creator.</li>
    <li><strong>ENTRIES</strong>: Total number of questions.</li>
    <li><strong>INSTRUCTIONS</strong>: Instructions shown to participants before the quiz begins.</li>
    <li><strong>LANGUAGE</strong>: Quiz language (reserved for future use).</li>
    <li><strong>PIN</strong>: The quiz PIN, shown to participants.</li>
    <li><strong>QUESTIONS</strong>: Number of questions participants must answer per attempt.</li>
    <li><strong>TITLE</strong>: Quiz title.</li>
    <li><strong>VERSION</strong>: File version for personal record.</li>
</ul>

<p>Example:</p>
<pre>
[DATABASE]
ADMIN        = "1001"
AUTHOR       = "Ioannis"
ENTRIES      = "12"
INSTRUCTIONS = "Exam instructions"
LANGUAGE     = "GR"
PIN          = "2000"
QUESTIONS    = "5"
TITLE        = "Sample Test"
VERSION      = "1.0"
</pre>

<h2>7. Question Sections (Q_x) in the Template</h2>
<p>
    Each question has an incremental number, from 1 to any number.
    Each question is stored in a section named Q_x, where x is the question number (e.g., Q_34 is the 34th question).
    Questions must be strictly in ascending order, and the total number of questions must also be stored in ENTRIES
    in the DATABASE section.
</p>

<ul>
    <li><strong>TITLE</strong>: The question text</li>
    <li><strong>ANSWER_1</strong> to <strong>ANSWER_4</strong>: The possible answers</li>
    <li><strong>CORRECT</strong>: The number of the correct answer (1–4)</li>
</ul>

<p>Example question:</p>
<pre>
[Q_1]
TITLE     = "Question text"
ANSWER_1  = "Answer 1"
ANSWER_2  = "Answer 2"
ANSWER_3  = "Answer 3"
ANSWER_4  = "Answer 4"
CORRECT   = 2
</pre>

<ul>
    <li>Questions must be in ascending order (Q_1, Q_2, Q_3…)</li>
    <li>No empty questions are allowed</li>
    <li><strong>CORRECT</strong> must be between 1 and 4</li>
    <li>Strings must be in quotes</li>
    <li>Numbers may be without quotes</li>
</ul>

<h2>8. What Quiz Creators Must Pay Attention To</h2>
<ul>
    <li>Do not change the structure of the file.</li>
    <li>Do not change field names.</li>
    <li>Do not leave empty questions.</li>
    <li>Use only 1–4 for CORRECT.</li>
    <li>Do not change the PIN after creating the file.</li>
    <li>Do not use special characters outside quotation marks.</li>
</ul>

<h2>9. Full Example File</h2>
<pre>
;<?php
;die();
;/*
[DATABASE]
ADMIN        = "1001"
AUTHOR       = "Author"
ENTRIES      = "3"
INSTRUCTIONS = "You will answer 2 random questions out of 3"
LANGUAGE     = "GR"
PIN          = "2000"
QUESTIONS    = "2"
TITLE        = "Sample Test"
VERSION      = "1.0"

[Q_1]
TITLE     = "What is 2+2?"
ANSWER_1  = "3"
ANSWER_2  = "4"
ANSWER_3  = "5"
ANSWER_4  = "6"
CORRECT   = 2

[Q_2]
TITLE     = "What is the capital of Greece?"
ANSWER_1  = "Athens"
ANSWER_2  = "Thessaloniki"
ANSWER_3  = "Patras"
ANSWER_4  = "Larissa"
CORRECT   = 1

[Q_3]
TITLE     = "What is the color of the sky?"
ANSWER_1  = "Red"
ANSWER_2  = "Blue"
ANSWER_3  = "Green"
ANSWER_4  = "Yellow"
CORRECT   = 2
*/
;?>
</pre>

</body>
</html>
