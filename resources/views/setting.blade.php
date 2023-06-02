
@extends('layouts.app')
@section('content')
<body>
<div class="section-heading" font-weight: 900;>
        Settings
    </div>

    <div class="form-box">
		<div class="button-box">
			<div id="btn"></div>
			<button type="button" class="toggle-btn" onclick="Click()">Disable</button>
			<button type="button" class="toggle-btn" onclick="rightClick()">Enable</button>
		</div>
	</div>


    <div class="containe">
  <form action="/action_page.php">
    <div class="row">
      <div class="col-25">
        <label for="email">Email Address</label>
      </div>
      <div class="col-75">
        <input type="text" id="fname" name="email" placeholder="Your email address.." required="">
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="Frequency">Frequency</label>
      </div>
      <div class="col-75">
      <select id="country" name="country">
          <option value="Monthly">Monthly</option>
          <option value="Weekly">Weekly</option>
          <option value="Daily">Daily</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="Schedule">Schedule</label>
      </div>
      <div class="col-75">
        <select id="country" name="Schedule">
          <option value="start Of Month">Start Of Month</option>
          <option value="End Of Month">End Of Month</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="Time">Time</label>
      </div>
      <div class="col-75">
      <input type="time" style="
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    resize: vertical;
">
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="Date">Date</label>
      </div>
      <div class="row">
      <div class="col-75">
      <input type="Date" style="
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    resize: vertical;
">
<br><br>
</div>
    </div>

    <div class="row">
      <input type="submit" value="Submit">
    </div>
  </form>
</div>

<script>
    var btn = document.getElementById('btn')

function leftClick() {
	btn.style.left = '0'
}

function rightClick() {
	btn.style.left = '110px'
}
</script>  


<style>
 .section-heading {
    background-color: #deecf9;
    /* border-bottom: 1px solid #d6dde3; */
    padding: 12px 18px;
    color: #253540;
    font-size: 16px;
    font-family: system-ui;
    font-weight: 400;
    line-height: 30px;
    box-sizing: border-box;
 }   
 * {
  box-sizing: border-box;
}

input[type=text], select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  resize: vertical;
}

label {
  padding: 12px 12px 12px 0;
  display: inline-block;
}

input[type=submit] {
  background-color: #04AA6D;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  float: right;
}

input[type=submit]:hover {
  background-color: #45a049;
}

.containe {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 90px;
}

.col-25 {
  float: left;
  width: 10%;
  margin-top: 13px;
}

.col-75 {
  float: left;
  width: 75%;
  margin-top: 6px;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .col-25, .col-75, input[type=submit] {
    width: 100%;
    margin-top: 0;
  }
}



body {
	background: #f2f2f2;
}

.button-box {
	width: 220px;
	margin: 35px auto;
	position: absolute;
	border-radius: 30px;
	background: white;
}

.toggle-btn {
	padding: 11px 31px;
	cursor: pointer;
	background: transparent;
	border: 0;
	outline: none;
	position: relative;
	text-align: center;
}

#btn {
	left: 0;
	top: 0;
	position: absolute;
	width: 123px;
	height: 104%;
	background: Gray;
	border-radius: 30px;
	transition: .5s;
}


</style>

@endsection