# IF3E Carpooling project

This directory contains all necessary files needed in order to run the IF3E Carpooling project.

# Team members
Only one member created the project, Étienne Pacault.

# How to run the project ?

In order to run the project, please make sure you have the necessary tool installed. You'll need php so you can launch a php server.
<br/>
You may use php on linux simply with the tools provided by XAMPP.
<br />
XAMPP is available at <a href="https://www.apachefriends.org/fr/index.html">https://www.apachefriends.org/fr/index.html</a>.
<br />
<br />

Once you've installed the software, php should be available at <code>/opt/lampp/bin/php</code>.
<br />
Just simply go to a folder in the PATH, such as <code>/usr/bin</code>
<br />
<code>cd /usr/bin</code>
<br />
<br/>
Then, simply create a symbolic link pointing to the real executable.
<br/>
<code>sudo ln -s /opt/lampp/bin/php</code>
<br />
<br />
Once this is done, you can go to the sources directory of the project.
<br />
<code>cd PLACE_WHERE_THE_PROJECT_IS/sources</code>
<br />
<br />
Then, you simply need to launch the server there.
<br />
<code>php -S localhost:8000</code>
<br />
You can change the 8000 if you prefer to launch the server on another port.
<br />
<br />
Finally, you just need to open your favorite web browser and go to :
<br />
<a href="http://localhost:8000">http://localhost:8000</a>
<br />
or change 8000 by the port you've set in the command you've used in order to launch the php server.
<br />
<br />

# Example accounts

When you start the project for the first time, there are three accounts that are already registered in the website :

- Étienne Pacault (me, the admin)
- Paul Dupont (a simple user)
- Jeanne Dubois (another simple user)

You can access to those accounts with those email and passwords :
- Email : etienne.pacault@protonmail.com | Password : 12345678 
- Email : paul.dupont@example.com | Password : 12345678
- Email : jeanne.dubois@example.com | Password : 12345678

# Implemented features

Currently, the website can handle an appropriate register/login system. Users may create there account with a form in the register page and can login.
The passwords are hashed in order to enforce security.

The main dashboard offers some options to the connected user.
- The user could search trips that correspond to his needs (appropriate departure and destination city, appropriate date for instance)
- If the user is willing to drive and to invite other users, he can register his car and its information and publish a new trip.
- The users who searched an appropriate trip could also book the trips they want and pay for them (fake bank application)
- The drivers can cancel their trips or mark them as completed allowing the users to send a review.
- The passengers can send a review and a rating to the driver.
- Finally, the user, if he has admins privileges, could also grant the admin status to other accounts and see the website statistics.

# Main weaknesses and limitations

The main weakness of the website is the lack of power in deleting or editing information.
The only way to edit or delete a user or a vehicle for instance is to access directly to the database using an appropriate software, since there are no way to do that in the website properly.

On top of that, admins do have really limited power in terms of managing users, they can only see statistics of the website and change the admin status of the accounts.
