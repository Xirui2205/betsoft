-- acl_role_id=10 is branch-owner
DELETE FROM vic_admin.acl_role_resource_privilege WHERE acl_role_id=10 AND acl_resource_id IN (
 SELECT acl_resource_id FROM vic_admin.acl_resource
 WHERE acl_resource_name IN ('kasa:REPORT_BALANCE','kasa:REPORT_PAYIN_TICKETS','kasa:REPORT_PAYOUT_TICKETS')
);
