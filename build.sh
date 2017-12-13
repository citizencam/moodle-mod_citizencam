#!/bin/sh

echo "Building mod_citizencam..."
module_name="citizencam"
script_name="build.sh"

cd amd && grunt amd -f && cd ../..
rm -f "$module_name.zip"
zip -r "$module_name.zip" "$module_name/" -x "$module_name/.git/*" citizencam/.gitignore "$module_name/$script_name"

echo
echo "Build successful!
If you want to test in a production environment, don't forget to turn \$CFG->cachejs to true in /var/www/moodle/config.php"