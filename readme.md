Lane Migration Tracker
======================

As [Lane](http://www.lanecc.edu) migrated it's pages from DreamWeaver and Contribute to a CMS (Drupal), we needed a place to keep track of what the old and new urls were.

Here's a sample workflow:
1. A student is going to migrate the content at /contact.html to a new page in the cms /contact. There is one link on /contact.html: to /about.html.
2. When creating the link to /about, the student looks at the migration tracker, to see if anyone has already migrated /about.html. If they have, the student knows the node number for /about, and can link that right away. If no one has, the student creates a new, empty, 'stub' page for /about, and creates an entry with the status 'Stub' for /about.html in the Migration Tracker.
3. The student creates an entry in the migration tracker for /contact, with the status 'Review'.
4. The student moves on to the next page, often selecting the next one with 'Stub' status in the tracker.

Meanwhile, an employee is able to skim the list, looking for pages that are set to 'Review'. After reviewing them for content and formatting, the employee changes the status to 'Complete'.

When a chunk - a group of pages roughly equal to a department or sub-folder - is all set to 'Complete', an employee would run a deployment script to query for pages set to 'Complete', and rewrite redirects on our CMS (which pointed stub and review pages to the old site), and generate a new .htaccess file for the new site, which redirected 'Complete' pages from the old site to the new.

Installation
------------
1. Rename config.defaut.php to config.php
2. Create a database and user for this application; record those in config.php
3. Echo sql.sql into your database. For example, `mysql -u user -ppassword tracker < sql.sql`

The table storage engine is set to MyISAM, since InnoDB doesn't log update times in Information_Schema. However, since MyISAM uses table level locking when doing a write, this may create a performance hit if you're getting more than a few hundred writes/second. But if this is the case, you've got a crazy migration strategy and you probably don't need this notice.

User Management
---------------
Unfortunately, we never got around to building out actual user management, since this was, ultimately, just a temporary project. To create a user, manually create them in the users table. Password hashes are of the form: `whirlpool($password . $salt)`. You can run a whirlpool hash [online](http://hash.online-convert.com/whirlpool-generator). To remove a user, just wipe out their password.

Further Configuration
---------------------
In addition to being used to login, emails are used to set icons for people via [Gravatar](http://en.gravatar.com). You can set the default gravatar (shown while not logged in) in index.html. 

Config.php contains the variable `$valid_urls`, which is used to try to check for typos in copying urls. Also, more practically, to remove the domain before displaying the url on the screen, to save space. Be sure to change it to reflect your migration source and target in order to avoid errors about invalid urls.

Security Considerations
-----------------------
This application was designed to run on a somewhat secure intranet, using locally unique passwords and has not been thoroughly vetted for security concerns. It's our recommendation that you take appropriate precautions.
