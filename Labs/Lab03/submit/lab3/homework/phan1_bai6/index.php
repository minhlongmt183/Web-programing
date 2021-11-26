<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registry Form</title>
</head>
<body>
	
	<form name="sign_up" action="validate.php" method="get">
		<table>
			<tr>
				<td>First name</td>
				<td>
					<input type="text" size="50" name="fname">
				</td>
			</tr>
			<tr>
				<td>Last name</td>
				<td>
					<input type="text" size="50" name="lname">
				</td>
			</tr>

			<tr>
				<td>Email</td>
				<td>
					<input type="text" size="50" name="email">
				</td>
			</tr>

			<tr>
				<td>Password</td>
				<td>
					<input type="text" size="50" name="passwd">
				</td>
			</tr>

			<tr>
				<td>Birthday</td>
				<td>
					<select id="select-date">
						<option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
						
					</select>
					<select id="select-month">
	                    <option value="1">January </option>
	                    <option value="2">February </option>
	                    <option value="3">March </option>
	                    <option value="4">April </option>
	                    <option value="5">May </option>
	                    <option value="6">June </option>
	                    <option value="7">July </option>
	                    <option value="8">August </option>
	                    <option value="9">September </option>
	                    <option value="10">October </option>
	                    <option value="11">November </option>
	                    <option value="12">December </option>
							
					</select>
                    <select id="select-year">
                        <option value="1993">1993</option>
                        <option value="1994">1994</option>
                        <option value="1995">1995</option>
                        <option value="1996">1996</option>
                        <option value="1997">1997</option>
                        <option value="1998">1998</option>
                        <option value="1999">1999</option>
                        <option value="2000">2000</option>
                        <option value="2001">2001</option>
                        <option value="2002">2002</option>
                        <option value="2003">2003</option>
                        <option value="2004">2004</option>
                        <option value="2005">2005</option>
                        <option value="2006">2006</option>
                        <option value="2007">2007</option>
                        <option value="2008">2008</option>
                        <option value="2009">2009</option>
                        <option value="2010">2010</option>
                        <option value="2011">2011</option>
                        <option value="2012">2012</option>
                        <option value="2013">2013</option>
                        <option value="2014">2014</option>
                        <option value="2015">2015</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>

                    </select>
				</td>
			</tr>
			<tr>
				<td>Gender</td>
				<td>
					<input type="radio" name="choice" value="male" id="male">
					<label for="male">Male</label>

					<input type="radio" name="choice" value="female" id="female">
					<label for="female">Female</label>

					<input type="radio" name="choice" value="unf" id="unf">
					<label for="unf">Unidentified</label>
				</td>
			</tr>
			<tr>
				<td>Country</td>
				<td>
					<select id="select-country">
						<option value="vn">Vietnam</option>
						<option value="aus">Australia</option>
						<option value="us">United State</option>
						<option value="ind">India</option>
						<option value="other">Other</option>
					</select>
				</td>
			</tr>

            <tr>
                <td>About: </td>
                <td>
                    <textarea name="about" maxlength="10000"  rows="3" cols="100" value="" ></textarea>
                </td>
            </tr>
			<tr>
				<td>
					<input type="submit" value="submit">
				</td>
				<td>
					<input type="button" value="reset">
				</td>
			</tr>


		</table>
		
	</form>
</body>
</html>