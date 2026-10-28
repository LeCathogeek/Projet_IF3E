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
