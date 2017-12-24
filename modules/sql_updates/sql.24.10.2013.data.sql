# insert admin
INSERT INTO admins(id, login, password) VALUES(1, 'admin', '827ccb0eea8a706c4c34a16891f84e7b');

#insert roles
INSERT INTO acl_roles (id, name) VALUES(1, 'admin'),(2, 'quest');

# insert permissions
INSERT INTO acl_permissions (id, name) VALUES(1, 'main index'), (2, 'admins index');

# insert perms-roles
INSERT INTO acl_permissionsroles(perm_id, role_id) VALUES (1,1), (2,1);