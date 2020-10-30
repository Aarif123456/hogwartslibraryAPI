# API for the Hogwarts library project #
API url: https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/ENDPOINT_NAME
Format for API description
1.Endpoint name
2. Description
3.File name - Since, code will always be updated even if the read me isn't
4. Parameter list -including which method to use such as POST, GET or REQUEST(both)
5. httpie command with expected result format to check if information about the command is updated

## Format ##
<details>
<summary> </summary>
<p>
2. Description: 
3. fileName.php --> /api/fileName
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

# Endpoints #
<details>
<summary> Verify users: </summary>
<p>
2. Description: Log the user in and then store the cookie
3. verifyUser.php  --> /api/verifyUser
4.Parameter list:
5.httpie command :
</details>

<details>
<summary> Check username </summary>
<p>
2. Description: Check if username is in database
3. checkUserName.php  --> /api/checkUserName
4.Parameter list:
5.httpie command :
</details>

<details>
<summary> Queries for charts accessible to all users </summary>
<p>
2. Description: Queries used to create charts and table that give meta information about the system. These queries can be accessed by guest users as well
3. guestCharts.php --> /api/guestCharts
4.Parameter list:
Accepts request variable: chartType
Current charts are:
category - 
major -
allTime -
houseFine - 

5.httpie command :
</details>

<details>
<summary> Authenticated Queries for charts </summary>
<p>
2. Description: Queries used to create charts and table that give meta information about the system. These can be accessed by the users with the correct privilege.

privilege list - where headmaster can access result from any query
headmaster -> librarian -> professor -> student

This API also allows default values for some users
So, if logged in as a student - you will receive result for the default student query
This is useful if the front-end is flexible and can handle different queries
Then we can change the default query in the back-end and the front-end will automatically be updated. However, this will cause them to be coupled. For this reason I recommend not relying on the default query and have only included them for backward compatibility.

3. loggedInChart.php --> /api/loggedInChart
4. Parameter list:
Accepts POST variable: chartType
5. httpie command :
</details>

<details>
<summary>Authenticated Queries for lists </summary>
<p>
2. Description: Queries used to create lists and tables that give meta information about the system. These can be <b>only</b> accessed by the users of the correct userType. So, to view the librarian queries you have to be a librarian. 

3. loadList.php --> /api/loadList
4. Parameter list:
Accepts POST variable: listType
5. httpie command :
</details>

<details>
<summary> Searching catalog </summary>
<p>
2. Description: API used to search the catalog. You can search for books title, author, ISBN, tag or keyword which searches through all of the other search method
3. search_div.php --> /api/search_div
4. Parameter list:
Accepts REQUEST variable: 
5. httpie command :searchType, searchWord

</details>

<details>
<summary> Viewing checked out books </summary>
<p>
2. Description: 
3. userCheckedOut.php --> /api/userCheckedOut
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Viewing fines on user's account</summary>
<p>
2. Description: 
3. userFines.php --> /api/userFines
4. Parameter list:
Accepts POST variable: listType: distinguish if we just want the total fine or a list of the transaction with fines
listType=getOutstandingFineOnAccount - retrieves the amount the user owes
listType=getTransactionWithFines - retrieves all fines where the user had a fine 
Future - May not show fines that are no longer relevant - e.g. under the total the has outstanding

5. httpie command :
</details>

<details>
<summary> Viewing holds on user account </summary>
<p>
2. Description: 
3. userHolds.php --> /api/userHolds
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary>add course </summary>
<p>
2. Description: Headmaster can add courses to the database
3. addCourses.php --> /api/addCourses
4. Parameter list: courseName & professorID
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> delete course</summary>
<p>
2. Description: Headmaster can delete courses from the database
3. deleteCourses.php --> /api/deleteCourses
4. Parameter list: courseID
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Enroll students in courses</summary>
<p>
2. Description: The headmaster can enroll students into the courses they are going to take this semester 
3. addEnrollment.php --> /api/addEnrollment
4. Parameter list: 
Accepts POST variable: courseID, studentID
5. httpie command :
</details>

<details>
<summary> Remove student from course</summary>
<p>
2. Description: The headmaster can also remove students from their classes
3. deleteEnrollment.php --> /api/deleteEnrollment
4. Parameter list: 
Accepts POST variable: enrollmentNumber
5. httpie command :
</details>

<details>
<summary> Make user's librarians</summary>
<p>
2. Description: Give user's librarian privileges
3. addLibrarian.php --> /api/addLibrarian
4. Parameter list:
Accepts POST variable: userID
5. httpie command :
</details>

<details>
<summary> Remove librarian</summary>
<p>
2. Description: This api will be used to revoke librarian privileges from an account. In the back-end we mark the librarian account as deactivated instead of deleting them, to ensure integrity

3. deleteLibrarian.php --> /api/deleteLibrarian
4. Parameter list:
Accepts POST variable: userID
5. httpie command :
</details>

<details>
<summary> Registering user </summary>
<p>
2. Description: 
3. addUser.php --> /api/addUser
4. Parameter list:
Accepts POST variable:  
5. httpie command :
Note: it might make sense to separate the endpoints for different user types. 

</details>

<details>
<summary> reset user password</summary>
<p>
2. Description: The headmaster has the power to reset user's passwords
3. resetPassword.php --> /api/resetPassword
4. Parameter list:
Accepts POST variable: uID, uPassword, developerPassword
5. httpie command :
 http -f --session=/tmp/session.json POST https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/resetPassword uID=userID uPassword='newPasssword' developerPassword='WhateverThedeveloperPasswordIs
</details>

<details>
### TODO Refactor the code for the following endpoints ###
<summary> check out book</summary>
<p>
2. Description: 
3. checkOut.php --> /api/checkOut
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> return book </summary>
<p>
2. Description: 
3. returnBook.php --> /api/returnBook
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> User pay off fine</summary>
<p>
2. Description: 
3. payFine.php --> /api/payFine
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Professor reserves book copies for class </summary>
<p>
2. Description: 
3. addReservation.php --> /api/addReservation
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary>Professor cancels reservation for book copies</summary>
<p>
2. Description: 
3. deleteReservation.php --> /api/deleteReservation
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> View books to reserve and list of current reservations</summary>
<p>
2. Description: 
3. reservationList.php --> /api/reservationList
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Put a hold on a book</summary>
<p>
2. Description: 
3. holdBook.php --> /api/holdBook
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Cancel a hold</summary>
<p>
2. Description: 
3. cancelHoldBook.php --> /api/cancelHoldBook
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Report book as lost</summary>
<p>
2. Description: 
3. lostBook.php --> /api/lostBook
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>

<details>
<summary> Renew a book</summary>
<p>
2. Description: 
3. renewBook.php --> /api/renewBook
4. Parameter list:
Accepts POST variable: 
5. httpie command :
</details>



