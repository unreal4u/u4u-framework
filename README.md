README
==============

u4u-framework
-------------

This framework was conceived originally as a simple and quick system to make some enterprise pages. In time, it scaled
up very quickly and many many modules later it grew up to be almost a CMS by itself.

As a very enthusiastic developer I didn't want this little system I built go lost, so I stripped out almost everything
it had related to the company I used to work for and built it again, this time doing things right and adopting standards
which I didn't have when I originally wrote this framework.

Finally, what began as a job ended up being a hobby: this framework is NOT a stable one, neither it is being currently
used in some enterprise: it is just used as a playground for me where I can grew up with some of the discoveries I've
made in time in PHP. If you want to use it, do it at your own risk! Any improvements are naturally welcome.

How to install
-------------

Sadly, there is no easy way to install this framework (yet). I might (or not) some day add an installer that works fine
with different databases and different setups, but in the meantime you can follow the main guidelines:

* First, check it out and install:

    <pre>git clone git@github.com:unreal4u/u4u-framework.git
    cd u4u-framework</pre>

* **Everything from this point on is just a sketch about how the install *SHOULD* be...**
* Next, make the database and import the initial data (will be replaced with an installer some day):

    <pre>@TODO</pre>

* Then you should be ready to go the installation program of your newly made app:

 <pre>http://[YOUR-SERVER]/u4u-framework/www/install/</pre>

General Usage
------------

**This section is still in development!**

### General considerations

This system has been set up taking into account a great versability for the developer and also for the end user. As
such, it it designed to serve as a general purpose framework so that you can get your job fastly done and on the other
side, maintaining some basic rules, it provides the users with a consistent user experience.

### Users and groups

This system is divided into users, which are always associated in groups. One user can belong to zero or more groups. At
the same time, there are also superusers, which have access to every bit in the system and generally override any custom
options.

### Pages

As a modular system, you can have:

* Multiple templates for the same logic (A mobile site is just a few templates away now)
* Multisite support
* Multiple languages support
* Etc.
