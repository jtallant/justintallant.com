# Comments

1. Add code to the provider create the comments.db file if it does not exist on boot
    * or create a command to set up the package (create the file)
1. Add the database to gitignore
1. run a command on your server to create the file
1. different db connection for local comments and production comments

## Notes
* Put your icon image in public/img/author.jpg


## Verify Email
1. User types in email address and submits
2. User receives email with token
3. User clicks link
4. User sees page verification success
5. Link on the page says back to comments (routes to entry they were going to comment on)