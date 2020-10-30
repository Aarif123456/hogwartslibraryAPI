# What is this? 🤔
An API built to be used by the for the [Hogwart's public library](https://abdullaharif.tech/hogwartslibrary/)

# API for the Hogwarts library project #
API url: https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/ENDPOINT_NAME

### Information in API description ###
* Endpoint name
* Description
* File name - Since, code will always be updated even if the read me isn't
* Parameter list -including which method to use such as POST, GET or REQUEST(both)
* httpie command with expected result format to check if information about the command is updated

## Format ##
<details>
<summary> </summary>

    1. Description: 
    2. fileName.php --> /api/fileName
    3. Parameter list:
    4. httpie command:
</details>

# Endpoints #
<details>
<summary> Verify users: </summary>

    1. Description: Log the user in and then store the cookie
    2. verifyUser.php  --> /api/verifyUser
    3. Parameter list:
    4. httpie command:

</details>

<details>
<summary> Check username </summary>

    1. Description: Check if username is in database
    2. checkUserName.php  --> /api/checkUserName
    3. Parameter list:
    4. httpie command:

</details>

<details>
<summary> Queries for charts accessible to all users </summary>

    1. Description: Queries used to create charts and table that give meta 
    information about the system. These queries can be accessed by guest users as well
    2. guestCharts.php --> /api/guestCharts
    3. Parameter list:
        Accepts request variable: chartType
        Current charts are:
        category - 
        major -
        allTime -
        houseFine - 
    4. httpie command:

</details>

<details>
<summary> Authenticated Queries for charts </summary>

    1. Description: Queries used to create charts and table that give meta 
    information about the system. These can be accessed by the users with the correct privilege.
        privilege list - where headmaster can access result from any query
        headmaster -> librarian -> professor -> student
        This API also allows default values for some users
        So, if logged in as a student - you will receive result for the default student query
        This is useful if the front-end is flexible and can handle different queries
        Then we can change the default query in the back-end and the front-end will automatically 
        be updated. However, this will cause them to be coupled. 
        For this reason I recommend not relying on the default query and have only 
        included them for backward compatibility.
    2. loggedInChart.php --> /api/loggedInChart
    3. Parameter list:
        Accepts POST variable: chartType
    4. httpie command:

</details>

<details>
<summary>Authenticated Queries for lists </summary>

    1. Description: Queries used to create lists and tables that give meta information 
    about the system. These can be only accessed by the users of the correct userType. 
    So, to view the librarian queries you have to be a librarian. 
    2. loadList.php --> /api/loadList
    3. Parameter list:
        Accepts POST variable: listType
    4. httpie command:

</details>

<details>
<summary> Searching catalog </summary>

    1. Description: API used to search the catalog. You can search for books title, 
    author, ISBN, tag or keyword which searches through all of the other search method
    2. search_div.php --> /api/search_div
    3. Parameter list:
        Accepts REQUEST variable: searchType, searchWord
    4. httpie command:


</details>

<details>
<summary> Viewing checked out books </summary>

    1. Description: 
    2. userCheckedOut.php --> /api/userCheckedOut
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Viewing fines on user's account</summary>

    1. Description: 
    2. userFines.php --> /api/userFines
    3. Parameter list:
        Accepts POST variable: listType: distinguish if we just want the total fine or a 
        list of the transaction with fines
        listType=getOutstandingFineOnAccount - retrieves the amount the user owes
        listType=getTransactionWithFines - retrieves all fines where the user had a fine 
        Future - May not show fines that are no longer relevant - e.g. over the 
        total outstanding fine on the user's account
    4. httpie command:
</details>

<details>
<summary> Viewing holds on user account </summary>

    1. Description: 
    2. userHolds.php --> /api/userHolds
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary>Add course </summary>

    1. Description: Headmaster can add courses to the database
    2. addCourses.php --> /api/addCourses
    3. Parameter list: courseName & professorID
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Delete course</summary>

    1. Description: Headmaster can delete courses from the database
    2. deleteCourses.php --> /api/deleteCourses
    3. Parameter list: courseID
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Enroll students in courses</summary>

    1. Description: The headmaster can enroll students into the courses they are going 
    to take this semester 
    2. addEnrollment.php --> /api/addEnrollment
    3. Parameter list: 
        Accepts POST variable: courseID, studentID
    4. httpie command:

</details>

<details>
<summary> Remove student from course</summary>

    1. Description: The headmaster can also remove students from their classes
    2. deleteEnrollment.php --> /api/deleteEnrollment
    3. Parameter list: 
        Accepts POST variable: enrollmentNumber
    4. httpie command:

</details>

<details>
<summary> Make user's librarians</summary>

    1. Description: Give user's librarian privileges
    2. addLibrarian.php --> /api/addLibrarian
    3. Parameter list:
        Accepts POST variable: userID
    4. httpie command:

</details>

<details>
<summary> Remove librarian</summary>

    1. Description: This api will be used to revoke librarian privileges from an account. 
    In the back-end we mark the librarian account as deactivated instead of 
    deleting them, to ensure integrity
    2. deleteLibrarian.php --> /api/deleteLibrarian
    3. Parameter list:
        Accepts POST variable: userID
    4. httpie command:

</details>

<details>
<summary> Registering user </summary>

    1. Description: 
    2. addUser.php --> /api/addUser
    3. Parameter list:
        Accepts POST variable:  
    4. httpie command:
*Note: it might make sense to separate the endpoints for different user types.*
</details>

<details>
<summary> Reset user password</summary>

    1. Description: The headmaster has the power to reset user's passwords
    2. resetPassword.php --> /api/resetPassword
    3. Parameter list:
        Accepts POST variable: uID, uPassword, developerPassword
    4. httpie command:
        http -f --session=/tmp/session.json POST https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/resetPassword uID=userID uPassword='newPasss' developerPassword='devPass'

</details>

### TODO Refactor the code for the following endpoints ###
<details>
<summary> Check out book</summary>

    1. Description: 
    2. checkOut.php --> /api/checkOut
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Return book </summary>

    1. Description: 
    2. returnBook.php --> /api/returnBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> User pay off fine</summary>

    1. Description: 
    2. payFine.php --> /api/payFine
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Professor reserves book copies for class </summary>

    1. Description: 
    2. addReservation.php --> /api/addReservation
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary>Professor cancels reservation for book copies</summary>

    1. Description: 
    2. deleteReservation.php --> /api/deleteReservation
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> View books to reserve and list of current reservations</summary>

    1. Description: 
    2. reservationList.php --> /api/reservationList
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Put a hold on a book</summary>

    1. Description: 
    2. holdBook.php --> /api/holdBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Cancel a hold</summary>

    1. Description: 
    2. cancelHoldBook.php --> /api/cancelHoldBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Report book as lost</summary>

    1. Description: 
    2. lostBook.php --> /api/lostBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Renew a book</summary>

    1. Description: 
    2. renewBook.php --> /api/renewBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>
