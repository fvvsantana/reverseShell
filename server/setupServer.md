# Open file: 
/etc/hosts
#and add line:
127.0.0.1       www.reverseShell.com

# Inside of file:
/opt/lampp/etc/httpd.conf
# Uncomment line:
Include etc/extra/httpd-vhosts.conf

# Inside of file:
/opt/lampp/etc/extra/httpd-vhosts.conf
# Append
```
<VirtualHost *:80>
    ServerName reverseShell
    ServerAlias www.reverseShell.com
    DocumentRoot "/home/flaviokc/Dropbox/git/php/reverseShell/server"
    ErrorLog "logs/reverseShellErrorLog"
    CustomLog "logs/reverseShellAccessLog" common
    <Directory "/home/flaviokc/Dropbox/git/php/reverseShell/server">
        Require all granted    
        Options +Indexes
    </Directory>
</VirtualHost>

```

# Finally restart server:
sudo /opt/lampp/lampp restart

# Now server should be running correctly. Try to access "www.reverseShell.com" on your browser.

# I also needed to do this command:
chmod -R 755 ~/Dropbox/

# Because some folders in the path of my server hadn't had the right permissions.



# Useful links from where I learned it:
# This tutorial doesn't work, but gives the idea:
https://ourcodeworld.com/articles/read/302/how-to-setup-a-virtual-host-locally-with-xampp-in-ubuntu

# This tutorial works, but it's not exactly what you want to do:
# Apache 2 : Host Multiple Websites On One Server With Single IP
https://www.youtube.com/watch?v=zPmYyLLGjmU

# This tutorial is for Windows 7, but gave me the idea of how to grant access to the folder
https://stackoverflow.com/questions/9110179/adding-virtualhost-fails-access-forbidden-error-403-xampp-windows-7


