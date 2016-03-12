--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;


--
-- Name: if_command_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('if_command_id_seq', 20, false);

--
-- Name: if_meta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('if_meta_id_seq', 16, false);


--
-- Name: if_option_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('if_option_id_seq', 25, false);



--
-- Name: if_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('if_user_id_seq', 14, false);


--
-- Name: if_usermeta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('if_usermeta_id_seq', 142, false);


--
-- Name: fusion_commands_command_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('fusion_commands_command_id_seq', 53, true);


--
-- Name: fusion_meta_meta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('fusion_meta_meta_id_seq', 21, true);


--
-- Name: fusion_options_option_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('fusion_options_option_id_seq', 33, true);


--
-- Name: fusion_usermeta_usermeta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('fusion_usermeta_usermeta_id_seq', 170, true);


--
-- Name: fusion_users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nagiosfusion
--

SELECT pg_catalog.setval('fusion_users_user_id_seq', 25, true);


--
-- Data for Name: fusion_commands; Type: TABLE DATA; Schema: public; Owner: nagiosfusion
--



--
-- Data for Name: fusion_meta; Type: TABLE DATA; Schema: public; Owner: nagiosfusion
--




--
-- Data for Name: fusion_options; Type: TABLE DATA; Schema: public; Owner: nagiosfusion
--

INSERT INTO fusion_options VALUES (25, 'subsystem_ticket', '95n2sn26');
INSERT INTO fusion_options VALUES (26, 'admin_name', 'Nagios Fusion Admin');
INSERT INTO fusion_options VALUES (27, 'admin_email', 'root@localhost');
INSERT INTO fusion_options VALUES (28, 'url', 'http://localhost/nagiosfusion/');
INSERT INTO fusion_options VALUES (29, 'default_language', 'en');
INSERT INTO fusion_options VALUES (30, 'default_theme', 'none');
INSERT INTO fusion_options VALUES (31, 'auto_update_check', '1');
INSERT INTO fusion_options VALUES (32, 'default_date_format', '1');
INSERT INTO fusion_options VALUES (33, 'default_number_format', '1');


--
-- Data for Name: fusion_usermeta; Type: TABLE DATA; Schema: public; Owner: nagiosfusion
--

INSERT INTO fusion_usermeta VALUES (149, 18, 'userlevel', '255', 1);



--
-- Data for Name: fusion_users; Type: TABLE DATA; Schema: public; Owner: nagiosfusion
--

INSERT INTO fusion_users VALUES (18, 'nagiosadmin', '40be4e59b9a2a2b5dffb918c0e86b3d7', 'Nagios Admin', 'root@localhost', '1234', 1);


--
-- PostgreSQL database dump complete
--

