CitizenCam Record resource plugin
==================================

Copyright: CitizenCam (https://citizencam.eu)
License: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


Description:
------------
CitizenCam Record enables you to insert a CitizenCam video recording directly into your course thanks to a multi-view player.


Installation:
-------------
Follow [the standard moodle procedure](https://docs.moodle.org/28/en/Installing_plugins) to install the module. You can install it from [the official Moodle plugins repository](https://moodle.org/plugins/view.php?plugin=mod_citizencam).

Once installed, go to the plugin settings in Moodle.

Set the URL of CitizenCam Studio and CitizenCam TV to use in the plugin settings (usually https://studio.citizencam.tv and https://www.citizencam.tv): 

![Plugin settings](https://puu.sh/xWehR/c4684ac6ec.png)


Usage:
------
Once configured, a new resource type is now available in Moodle.

![Use 1](https://puu.sh/xWeYD/6f79f5b20c.png)

The user is then able to select a CitizenCam event to display in their course, by clicking on an event from the events grid.

![Use 2](https://puu.sh/xWeYI/a73eecfb47.png)

A window with details about the event is displayed. Confirm your choice.

![Use 3](https://puu.sh/xWeYG/0dfbf2d868.png)

That's it, the video is now integrated in your Moodle course.

![Use 4](https://puu.sh/xWeYF/363cab413f.png)


Dependencies:
-------------
This plugin requires the PHP CURL library, Moodle >= 3.2.2.