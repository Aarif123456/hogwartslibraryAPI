# What is this? 🤔
An API built to be used by the for the [Hogwart's public library](https://abdullaharif.tech/hogwartslibrary/)

# API for the Hogwarts library project #
API url: https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/ENDPOINT_NAME

### Information in API description ###
* Endpoint name
* Description
* File name - Since, code will always be updated even if the read me isn't
* Parameter list -including which method to use such as POST, GET or REQUEST(both)
* httpie command with expected result format to validate the listed information about the endpoint

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
        Accepts POST variable:  username, password, userType
        userType (valid values) : professor, student, headmaster, user, librarian
    4. httpie command:
        http --session=/tmp/session.json --form POST https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/verifyUser username='hGranger' password='actors1Reserve9Vegas7Portugal9Plants' userType='librarian'
</details>

<details>
<summary> Registering user </summary>

    1. Description: 
    2. userManagement/addUser.php --> /api/userManagement/addUser
    3. Parameter list:
        Accepts POST variable: fname, lname, userType, username, password, passcode
        // IF user is a student we need parameter of house and major
        // If professor we need department
        userType (valid values) : professor, student, headmaster
        
    4. httpie command:
        http --session=/tmp/session.json --form POST https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/userManagement/addUser username='JKRowling' password='whats in a dream' userType='professor'  fname='Joanne' lname='Rowling' department='English' passcode='ENTER_THE_PASSCODE'
   
*Note: it might make sense to separate the endpoints for different user types.*
</details>

<details>
<summary> Check username </summary>

    1. Description: Check if username is in database
    2. userManagement/checkUserName.php  --> /api/userManagement/checkUserName
    3. Parameter list: 
         Accepts POST variable:  username
    4. httpie command:

</details>

<details>
<summary> Reset user password</summary>

    1. Description: The headmaster has the power to reset user's passwords
    2. userManagement/resetPassword.php --> /api/userManagement/resetPassword
    3. Parameter list:
        Accepts POST variable: uID, uPassword, developerPassword
    4. httpie command:
        http -f --session=/tmp/session.json POST https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/resetPassword uID=userID uPassword='newPasss' developerPassword='devPass'

</details>

<details>
<summary> Queries for charts accessible to all users </summary>

    1. Description: Queries used to create charts and table that give meta 
    information about the system. These queries can be accessed by guest users as well
    2. chart/guestCharts.php --> /api/chart/guestCharts
    3. Parameter list:
        Accepts request variable: chartType
        Current charts are: category,  major, allTime, houseFine, 
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
    2. chart/loggedInChart.php --> /api/chart/loggedInChart
    3. Parameter list:
        Accepts POST variable: chartType
        if headmaster
            valid values: reservationList ,fineList ,activeList
        if librarian
            valid values: unusedList, lostList
        if professor
            valid values: activityPerMajorAndDepartment
        if student
            valid values: numBooksPerCategory
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
        if headmaster
            valid values: reservationList ,fineList ,activeList
        if librarian
            valid values: unusedList, lostList
    4. httpie command:

</details>

<details>
<summary> Viewing checked out books </summary>

    1. Description: 
    2. user/userCheckedOut.php --> /api/user/userCheckedOut
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Viewing fines on user's account</summary>

    1. Description: 
    2. user/userFines.php --> /api/user/userFines
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
    2. user/userHolds.php --> /api/user/userHolds
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary>Add course </summary>

    1. Description: Headmaster can add courses to the database
    2. headmaster/addCourses.php --> /api/headmaster/addCourses
    3. Parameter list: courseName & professorID
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Delete course</summary>

    1. Description: Headmaster can delete courses from the database
    2. headmaster/deleteCourses.php --> /api/headmaster/deleteCourses
    3. Parameter list: courseID
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Enroll students in courses</summary>

    1. Description: The headmaster can enroll students into the courses they are going 
    to take this semester 
    2. headmaster/addEnrollment.php --> /api/headmaster/addEnrollment
    3. Parameter list: 
        Accepts POST variable: courseID, studentID
    4. httpie command:

</details>

<details>
<summary> Remove student from course</summary>

    1. Description: The headmaster can also remove students from their classes
    2. headmaster/deleteEnrollment.php --> /api/headmaster/deleteEnrollment
    3. Parameter list: 
        Accepts POST variable: enrollmentNumber
    4. httpie command:

</details>

<details>
<summary> Make user's librarians</summary>

    1. Description: Give user's librarian privileges
    2. headmaster/addLibrarian.php --> /api/headmaster/addLibrarian
    3. Parameter list:
        Accepts POST variable: userID
    4. httpie command:

</details>

<details>
<summary> Remove librarian</summary>

    1. Description: This api will be used to revoke librarian privileges from an account. 
    In the back-end we mark the librarian account as deactivated instead of 
    deleting them, to ensure integrity
    2. headmaster/deleteLibrarian.php --> /api/headmaster/deleteLibrarian
    3. Parameter list:
        Accepts POST variable: userID
    4. httpie command:

</details>

<details>
<summary> Check out book</summary>

    1. Description: Endpoint used by librarian's to checkout book to users
    2. librarian/checkOut.php --> /api/librarian/checkOut
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Return book </summary>

    1. Description:  Endpoint used by librarian's to return book for users. When book is returned we add fine to the user's account, store the librarian who returned the book. We also decrement the numberOfBooks on the members account. And, if the book is on hold, we reserve the book to the first user in line.
    2. librarian/returnBook.php --> /api/librarian/returnBook
    3. Parameter list: 
        Accepts POST variable: bookBarcode
    4. httpie command:
         http -f --session=/tmp/session.json POST https://arif115.myweb.cs.uwindsor.ca/hogwartslibrary/api/librarian/returnBook bookBarcode=

</details>

<details>
<summary> Searching catalog </summary>

    1. Description: API used to search the catalog. You can search for books title, 
    author, ISBN, tag or keyword which searches through all of the other search method
    2. library/searchCatalogue.php --> /api/library/searchCatalogue
    3. Parameter list:
        Accepts REQUEST variable: searchType, searchWord
    4. httpie command:

</details>

<details>
<summary> User pay off fine</summary>

    1. Description: 
    2. librarian/payFine.php --> /api/librarian/payFine
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Put a hold on a book</summary>

    1. Description: 
    2. library/holdBook.php --> /api/library/holdBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Cancel a hold</summary>

    1. Description: 
    2. library/cancelHoldBook.php --> /api/library/cancelHoldBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Renew a book</summary>

    1. Description: 
    2. library/renewBook.php --> /api/library/renewBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

### TODO Refactor the code for the following endpoints ###

<details>
<summary> Report book as lost</summary>

    1. Description: 
    2. library/lostBook.php --> /api/library/lostBook
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> Professor reserves book copies for class </summary>

    1. Description: 
    2. reservation/addReservation.php --> /api/reservation/addReservation
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary>Professor cancels reservation for book copies</summary>

    1. Description: 
    2. reservation/deleteReservation.php --> /api/reservation/deleteReservation
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

<details>
<summary> View books to reserve and list of current reservations</summary>

    1. Description: 
    2. reservation/reservationList.php --> /api/reservation/reservationList
    3. Parameter list:
        Accepts POST variable: 
    4. httpie command:

</details>

## Potential changes ##
It might make more sense to change the express designation to bookItem level instead of book level
Also, need to add an endpoint for addBooks, logout, closing account