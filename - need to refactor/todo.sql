1) If we add a new book add total copy by one and make the current date today
BEGIN
    IF (NEW.purchaseDate IS NULL) THEN 
        SET NEW.purchaseDate = CURDATE();
    END IF;
     
END

2)

/*During checking out reduce the number of remaining copies of the book by one - why ?
Do the opposite during returnDate*/
#// Instead when renewing books if the item exists in the hold table then do not allow user to renew - or if the count in the hold table + count in 

BEGIN
DECLARE num SMALLINT Default 0;
# If not in lost case
IF NEW.lostDate IS NULL && OLD.lostDate IS NULL THEN 



    # If the book no longer has a pending hold, then we fix up the queue number - but we want to remove the whole 
    # queue number system and replace with smallest timestamps LIMIT 1
    IF NEW.onHold= 1 AND  OLD.onHold =0 THEN 
    SELECT MIN(IFNULL(queueNumber,0)) AS num1 INTO num FROM holds where bookISBN = OLD.bookISBN;
        UPDATE holds  
        SET queueNumber = queueNumber - num
        WHERE bookISBN = OLD.bookISBN;
    END IF;

    # If the book is now put on active hold 
    IF NEW.onHold =0 AND OLD.onHold =1 THEN 
        
    END IF;
END IF;




Cases

1) check-out
- UPDATE books SET remainingCopies = remainingCopies -1 WHERE bookISBN = OLD.bookISBN;
- UPDATE transactions SET borrowedDate = CURDATE();
- UPDATE transactions SET dueDate = DATE_ADD(CURDATE(), INTERVAL $NORMAL_LENDING_DAYS DAY); # When normal for express 10 days
- UPDATE members SET numBooks = numBooks +1 where memberID = NEW.borrowedBy;
- UPDATE bookItem SET status = 3  WHERE bookBarcode = NEW.bookBarcode;
2)returning
- UPDATE books SET remainingCopies = remainingCopies +1 WHERE bookISBN = OLD.bookISBN;

3)renewing
#  in transaction if renew <2
- SET NEW.dueDate = DATE_ADD(CURDATE(), INTERVAL 21 DAY);
- SET NEW.renewedTime = OLD.renewedTime +1;
- SET modifyDate =CURDATE();
4)holding
# update the number of hold on the books - can be counted with query so remove - maybe keep since, this is useful information to show and is easy to track
UPDATE books SET `holds` = `holds` +1 WHERE bookISBN = NEW.bookISBN; 

IF NEW.reservedCopy IS NULL THEN 
    SET NEW.queueNumber = (SELECT MAX(`queueNumber`)  FROM holds WHERE bookISBN = NEW.bookISBN) +1;
END IF;   # If we haven't found a copy then the queue number will be the high queue -1 - replace with timestamp

# default queue number is zero is no longer relevant
# if there are no available copies then make the holds expire in one year
UPDATE holds SET NEW.holdExpiryDate  = DATE_ADD(CURDATE(), INTERVAL 1 YEAR);
# else if there is a reserved copy 
UPDATE holds SET NEW.holdExpiryDate  = DATE_ADD(CURDATE(), INTERVAL 1 MONTH);
4.1) Hold complete or canceled
UPDATE books SET `holds` = `holds` -1 WHERE bookISBN = OLD.bookISBN AND `holds` > 0;

5) buying book
-  SET NEW.purchaseDate = CURDATE();

6) Reporting lost
        UPDATE transactions SET fine = fine + OLD.price WHERE bookBarcode = OLD.bookBarcode AND returnDate IS NULL;
        UPDATE books SET totalCopies = totalCopies -1 WHERE bookISBN = OLD.bookISBN;
        UPDATE transactions SET NEW.reservedFor = NULL;
        UPDATE holds SET reservedCopy=NULL WHERE reservedCopy=OLD.bookBarcode;    
        # change to status 
        UPDATE bookItem SET NEW.onHold =1;
7) returning lost book
    # SET modifyDate = NEW.returnDate; - use the current date as the return date
    # modify the hold date if we had a hold - I don't think this will work since we won't automatically assign the reserved copy 
    UPDATE holds SET holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH) WHERE reservedCopy = OLD.bookBarcode;
    # Change status from lost to available
    UPDATE bookItem SET `status`= 1 WHERE bookBarcode = OLD.bookBarcode AND `status` = 4; 
    # decrease the book count
    UPDATE members SET numBooks = numBooks -1 WHERE memberID = OLD.borrowedBy;

    UPDATE transactions SET fine = fine - (OLD.price *0.85) WHERE bookBarcode = OLD.bookBarcode AND returnDate IS NULL;
    UPDATE books SET totalCopies = totalCopies +1 WHERE bookISBN = OLD.bookISBN;
8) calculate fine for return, renew and lost
    # If the date returned or renewed is after the fine then update fine on transaction 
    IF(OLD.dueDate < modifyDate) THEN
        SET NEW.fine = OLD.fine + 
        ABS(DATEDIFF(modifyDate,OLD.dueDate))*0.30;
    END IF;

    # current code to update fine -but to simplify we can add fine to member table only if returned or lost 
    IF NEW.fine != OLD.fine THEN # IF NEW FINE NOT zero
        UPDATE members SET fines = fines + NEW.fine - OLD.fine WHERE memberID =OLD.borrowedBy;
    END IF;

    # Blacklist students with a fine >10 or professor greater than 50

9) reserving an avilable copy # for cancel hold, return or doing the original hold
    # DOn't do this instead mark as completed - when returning or canceled if canceled 
    DELETE FROM holds WHERE reservedCopy = OLD.bookBarcode AND queueNumber =0;
    # if first on queue - then find smallest timestamp and reserve a copy for them
    SELECT MIN(IFNULL(queueNumber,0)) AS num1 INTO num FROM holds where bookISBN = OLD.bookISBN and queueNumber >0;
    IF num !=0 THEN
        #  then we fix up the queue number - but we want to remove the whole 
        # queue number system and replace with smallest timestamps LIMIT 1
        UPDATE holds SET queueNumber =queueNumber -num WHERE BookISBN = OLD.bookISBN AND queueNumber >1 and reservedCopy IS NULL ;
        # replace with setting the hold to reserved copy for the smallest timestamps 
        UPDATE holds SET reservedCopy = OLD.bookBarcode WHERE BookISBN = OLD.bookISBN AND queueNumber =0 and reservedCopy IS NULL ;
        UPDATE transactions SET NEW.onHold =1;
    END IF;

) create php file with constant list
- SECRET_KEY
- $NORMAL_LENDING_DAYS=21
- $EXPRESS_LENDING_DAYS=10
- $FINE_PER_DAY_OVERDUE=0.3
- $REFUND_FOR_FOUND_BOOK=0.85 // 85% of book refunded


- status meaning
- 
