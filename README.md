Read Me!
Welcome to BrewEase: Coffee Shop Order Management System. Here is the manual for you :)
***************************************************************************************************************************************
1. Landing Page:
- Please go to [https://brewease](http://localhost/brewease/portal.php)
- It integrates all users system; Manager, Cashier, and Customer.
- Upon clicking "Login as Manager" or "Login as Cashier" or "Continue as Customer", system will redirects to their respective dashboards.
=======================================================================================================================================
2. Sign Up:
- Only customer can sign up an account for the system. Manager's account is predefined in database, while Cashier's account is created 
by Manager in User Management module.
- Customer need to adhere to unique username, valid email format, minimum 6 password lengths, and matching password in password and 
confirm password field.
=======================================================================================================================================
3. Login:
- Use username 'manager' and password 'password123' to login as Manager.
- Cashier's account is created by Manager in User Management module.
- Customer should register an account first before they can login.
=======================================================================================================================================
4. User Management:
- Manager can create, edit, and delete cashier account.
- If Manager edit the cashier's account from "Active" to "Inactive", the Cashier can no longer login to the system.
=======================================================================================================================================
5. Menu Management:
- Manager can view, add, update, and delete menu item.
- By setting Status to "Unavailable", it will soft-delete the item from menu.
- The menu will be reflected on all user's view.
=======================================================================================================================================
6. Cart Management:
- Customer and cashier can add item to cart with customization of sugar level, addon, and quantity.
- They can also edit or delete the item in cart before checkout.
- Orders placed by Customers will be recorded as "Offline Customer" in the database. No points will be awarded.
=======================================================================================================================================
7. Payment Management:
- Only dummy payment are made. Customer must fill in the form and choose any of the payment method before checkout.
- Upon successful checkout, customer will be awarded 1 point for every RM1 spent.
=======================================================================================================================================
8. Order Management:
- Manager and Cashier can view orders placed (order id, customer name, payment method, total, date, status, and action).
- For each order, they can take action to view the order details; order information and item details.
- They can also update the status. By default, order is set to "Preparing". They can update it to "Ready" or "Completed".
=======================================================================================================================================
9. Loyalty Points Management:
- Upon successful checkout, Customer's points will be increased according to the amount they spent.
- If points ≥ 1000, Customer can redeem a free drink worth RM10 on their next checkout.
- Customer can choose any drinks, but if the price of the drinks is > RM 10, they need to pay the remaining.
- Upon successful redemption, 1000 points will be deducted from total points.
- Manager can view Customer's loyalty points on loyalty section.
- Customer can view their profile section to see the account details; name, email, loyalty points.
- Customer can view their loyalty section to view their loyalty point history where it records the points earned and redeemed, 
along with date and order id.
=======================================================================================================================================
10. Sales Analytics Management:
- Manager can choose the date they want to analyse sales report.
- The report will be updated dynamically, showing the total revenue, total orders, top customer along with the amount they spent, top 5 
products, and least purchased products.
- Manager can export the report as PDF or CSV.
=======================================================================================================================================
