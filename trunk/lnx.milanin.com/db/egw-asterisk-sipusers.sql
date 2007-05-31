create or replace view sipusers as select a.account_lid as name, 'members' as context,a.account_pwd as md5secret, 'dynamic' as host  from phpgw_accounts a
