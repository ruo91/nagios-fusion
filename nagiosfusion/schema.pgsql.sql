--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

--
-- Name: if_command_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE if_command_id_seq
    START WITH 20
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.if_command_id_seq OWNER TO nagiosfusion;


--
-- Name: if_meta_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE if_meta_id_seq
    START WITH 16
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.if_meta_id_seq OWNER TO nagiosfusion;

--
-- Name: if_option_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE if_option_id_seq
    START WITH 25
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.if_option_id_seq OWNER TO nagiosfusion;

--
-- Name: if_sysstat_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE if_sysstat_id_seq
    START WITH 15
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.if_sysstat_id_seq OWNER TO nagiosfusion;


--
-- Name: if_user_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE if_user_id_seq
    START WITH 14
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.if_user_id_seq OWNER TO nagiosfusion;

--
-- Name: if_usermeta_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE if_usermeta_id_seq
    START WITH 142
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.if_usermeta_id_seq OWNER TO nagiosfusion;


--
-- Name: fusion_commands_command_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_commands_command_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_commands_command_id_seq OWNER TO nagiosfusion;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: fusion_commands; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_commands (
    command_id integer DEFAULT nextval('fusion_commands_command_id_seq'::regclass) NOT NULL,
    group_id integer DEFAULT 0,
    submitter_id integer DEFAULT 0,
    beneficiary_id integer DEFAULT 0,
    command integer NOT NULL,
    submission_time timestamp without time zone NOT NULL,
    event_time timestamp without time zone NOT NULL,
    frequency_type integer DEFAULT 0,
    frequency_units integer DEFAULT 0,
    frequency_interval integer DEFAULT 0,
    processing_time timestamp without time zone,
    status_code integer DEFAULT 0,
    result_code integer DEFAULT 0,
    command_data text,
    result text
);


ALTER TABLE public.fusion_commands OWNER TO nagiosfusion;

--
-- Name: fusion_events; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_events (
    event_id integer NOT NULL,
    event_time timestamp without time zone,
    event_source smallint,
    event_type smallint DEFAULT 0 NOT NULL,
    status_code smallint DEFAULT 0 NOT NULL,
    processing_time timestamp without time zone
);


ALTER TABLE public.fusion_events OWNER TO nagiosfusion;

--
-- Name: fusion_events_event_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_events_event_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_events_event_id_seq OWNER TO nagiosfusion;

--
-- Name: fusion_events_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nagiosfusion
--

--- ALTER SEQUENCE fusion_events_event_id_seq OWNED BY fusion_events.event_id;


--
-- Name: fusion_meta_meta_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_meta_meta_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_meta_meta_id_seq OWNER TO nagiosfusion;

--
-- Name: fusion_meta; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_meta (
    meta_id integer DEFAULT nextval('fusion_meta_meta_id_seq'::regclass) NOT NULL,
    metatype_id integer DEFAULT 0,
    metaobj_id integer DEFAULT 0,
    keyname character varying(128) NOT NULL,
    keyvalue text
);


ALTER TABLE public.fusion_meta OWNER TO nagiosfusion;

--
-- Name: fusion_options_option_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_options_option_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_options_option_id_seq OWNER TO nagiosfusion;

--
-- Name: fusion_options; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_options (
    option_id integer DEFAULT nextval('fusion_options_option_id_seq'::regclass) NOT NULL,
    name character varying(64) NOT NULL,
    value text
);


ALTER TABLE public.fusion_options OWNER TO nagiosfusion;


--
-- Name: fusion_sysstat_sysstat_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_sysstat_sysstat_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_sysstat_sysstat_id_seq OWNER TO nagiosfusion;

--
-- Name: fusion_sysstat; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_sysstat (
    sysstat_id integer DEFAULT nextval('fusion_sysstat_sysstat_id_seq'::regclass) NOT NULL,
    metric character varying(128) NOT NULL,
    value character varying(4096),
    update_time timestamp without time zone NOT NULL
);


ALTER TABLE public.fusion_sysstat OWNER TO nagiosfusion;


--
-- Name: fusion_usermeta_usermeta_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_usermeta_usermeta_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_usermeta_usermeta_id_seq OWNER TO nagiosfusion;

--
-- Name: fusion_usermeta; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_usermeta (
    usermeta_id integer DEFAULT nextval('fusion_usermeta_usermeta_id_seq'::regclass) NOT NULL,
    user_id integer NOT NULL,
    keyname character varying(255) NOT NULL,
    keyvalue text,
    autoload smallint DEFAULT (0)::smallint
);


ALTER TABLE public.fusion_usermeta OWNER TO nagiosfusion;

--
-- Name: fusion_users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: nagiosfusion
--

CREATE SEQUENCE fusion_users_user_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_users_user_id_seq OWNER TO nagiosfusion;

--
-- Name: fusion_users; Type: TABLE; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE TABLE fusion_users (
    user_id integer DEFAULT nextval('fusion_users_user_id_seq'::regclass) NOT NULL,
    username character varying(64) NOT NULL,
    "password" character varying(64) NOT NULL,
    name character varying(64),
    email character varying(128) NOT NULL,
    backend_ticket character varying(128),
    enabled smallint DEFAULT 1::smallint NOT NULL
);


ALTER TABLE public.fusion_users OWNER TO nagiosfusion;


-- 
-- ////////////////////////DEVELOPMENT EXPERIMENTS////////////
--

CREATE SEQUENCE fusion_tac_data_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.fusion_tac_data_id_seq OWNER TO nagiosfusion;
-- Name fusion_tacstatus; Type:  TABLE; Schema: public; Owner: nagiosfusion; Tablespace:
--
CREATE TABLE fusion_tac_data (
    server_id integer DEFAULT nextval('fusion_tac_data_id_seq'::regclass) NOT NULL, 
	server_sid varchar(32) NOT NULL,
	server_name varchar(128) NOT NULL,
	valid_credentials smallint DEFAULT 1::smallint NOT NULL,
    last_update_time timestamp without time zone,
	server_is_reachable smallint DEFAULT 1::smallint NOT NULL,
	error smallint DEFAULT 0::smallint NOT NULL,
	error_message varchar(256),

	
	hosts_down_total integer DEFAULT 0,
	hosts_down_unhandled integer DEFAULT 0,
	hosts_down_scheduleddowntime integer DEFAULT 0,
	hosts_down_acknowledged integer DEFAULT 0,
	hosts_down_disabled integer DEFAULT 0,
	
	hosts_unreachable_total integer DEFAULT 0,
	hosts_unreachable_unhandled integer DEFAULT 0,
	hosts_unreachable_scheduleddowntime integer DEFAULT 0,
	hosts_unreachable_acknowledged integer DEFAULT 0,
	hosts_unreachable_disabled integer DEFAULT 0,	
	
	hosts_up_total integer DEFAULT 0,	
	hosts_up_disabled integer DEFAULT 0,	
	
	hosts_pending_total integer DEFAULT 0,	
	hosts_pending_disabled integer DEFAULT 0,	
	
	services_critical_total integer DEFAULT 0,
	services_critical_unhandled integer DEFAULT 0,
	services_critical_scheduleddowntime integer DEFAULT 0,
	services_critical_acknowledged integer DEFAULT 0,
	services_critical_disabled integer DEFAULT 0,
	services_critical_hostproblem integer DEFAULT 0,	
	
	services_warning_total integer DEFAULT 0,
	services_warning_unhandled integer DEFAULT 0,
	services_warning_scheduleddowntime integer DEFAULT 0,
	services_warning_acknowledged integer DEFAULT 0,
	services_warning_disabled integer DEFAULT 0,	
	services_warning_hostproblem integer DEFAULT 0,

	services_unknown_total integer DEFAULT 0,
	services_unknown_unhandled integer DEFAULT 0,
	services_unknown_scheduleddowntime integer DEFAULT 0,
	services_unknown_acknowledged integer DEFAULT 0,
	services_unknown_disabled integer DEFAULT 0,	
	services_unknown_hostproblem integer DEFAULT 0,

	services_ok_total integer DEFAULT 0,	
	services_ok_disabled integer DEFAULT 0,	
	
	services_pending_total integer DEFAULT 0,	
	services_pending_disabled integer DEFAULT 0,	
	
	flap_detection smallint DEFAULT 1::smallint NOT NULL,
	notifications smallint DEFAULT 1::smallint NOT NULL,
	event_handlers smallint DEFAULT 1::smallint NOT NULL,
	active_checks smallint DEFAULT 1::smallint NOT NULL,
	passive_checks smallint DEFAULT 1::smallint NOT NULL
	
	 
);

ALTER TABLE public.fusion_tac_data OWNER TO nagiosfusion;

CREATE TABLE fusion_sessions (
	session_id varchar(128) NOT NULL,
	last_update_time timestamp without time zone
	);
	  
ALTER TABLE public.fusion_sessions OWNER TO nagiosfusion;



--
--	Recent alerts table 
-- 
CREATE SEQUENCE fusion_recent_alerts_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE fusion_recent_alerts (
    id integer DEFAULT nextval('fusion_recent_alerts_id_seq'::regclass) NOT NULL, 
	server_sid varchar(32) NOT NULL,
	server_name varchar(64) NOT NULL,
	host_name varchar(128) NOT NULL,
	service_description varchar(128) NOT NULL,
	type varchar(32) NOT NULL,
	state smallint DEFAULT 0,
	start_time timestamp without time zone,
	contact_name varchar(64),
	notification_command varchar(64),
	output varchar(512)
);

ALTER TABLE public.fusion_recent_alerts OWNER TO nagiosfusion;

-- -------------------------------------------------------

--
--	Top Alert Producers table 
-- 
CREATE SEQUENCE fusion_topalertproducers_id_seq
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE fusion_topalertproducers (
   id integer DEFAULT nextval('fusion_topalertproducers_id_seq'::regclass) NOT NULL, 
	server_sid varchar(32) NOT NULL,
	server_name varchar(64) NOT NULL,
	host_name varchar(128) NOT NULL,
	service_description varchar(128) NOT NULL,
	rank smallint NOT NULL,
	alert_count smallint DEFAULT 0
);

ALTER TABLE public.fusion_topalertproducers OWNER TO nagiosfusion;







--
-- Name: event_id; Type: DEFAULT; Schema: public; Owner: nagiosfusion
--

ALTER TABLE fusion_events ALTER COLUMN event_id SET DEFAULT nextval('fusion_events_event_id_seq'::regclass);


--
-- Name: fusion_commands_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_commands
    ADD CONSTRAINT fusion_commands_pkey PRIMARY KEY (command_id);


--
-- Name: fusion_events_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_events
    ADD CONSTRAINT fusion_events_pkey PRIMARY KEY (event_id);


--
-- Name: fusion_meta_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_meta
    ADD CONSTRAINT fusion_meta_pkey PRIMARY KEY (meta_id);


--
-- Name: fusion_options_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_options
    ADD CONSTRAINT fusion_options_pkey PRIMARY KEY (option_id);


--
-- Name: fusion_usermeta_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_usermeta
    ADD CONSTRAINT fusion_usermeta_pkey PRIMARY KEY (usermeta_id);


--
-- Name: fusion_usermeta_user_id_key; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_usermeta
    ADD CONSTRAINT fusion_usermeta_user_id_key UNIQUE (user_id, keyname);


--
-- Name: fusion_sysstat_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_sysstat
    ADD CONSTRAINT fusion_sysstat_pkey PRIMARY KEY (sysstat_id);


	
--
-- Name: fusion_users_pkey; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_users
    ADD CONSTRAINT fusion_users_pkey PRIMARY KEY (user_id);


--
-- Name: fusion_users_username_key; Type: CONSTRAINT; Schema: public; Owner: nagiosfusion; Tablespace: 
--

ALTER TABLE ONLY fusion_users
    ADD CONSTRAINT fusion_users_username_key UNIQUE (username);


--
-- Name: event_time; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX event_time ON fusion_events USING btree (event_source);


--
-- Name: fusion_commands_event_time_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_commands_event_time_idx ON fusion_commands USING btree (event_time);


--
-- Name: fusion_meta_keyname_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_meta_keyname_idx ON fusion_meta USING btree (keyname);


--
-- Name: fusion_meta_metaobj_id_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_meta_metaobj_id_idx ON fusion_meta USING btree (metaobj_id);


--
-- Name: fusion_meta_metatype_id_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_meta_metatype_id_idx ON fusion_meta USING btree (metatype_id);


--
-- Name: fusion_options_name_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_options_name_idx ON fusion_options USING btree (name);


--
-- Name: fusion_usermeta_autoload_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_usermeta_autoload_idx ON fusion_usermeta USING btree (autoload);



--
-- Name: fusion_sysstat_metric_idx; Type: INDEX; Schema: public; Owner: nagiosfusion; Tablespace: 
--

CREATE INDEX fusion_sysstat_metric_idx ON fusion_sysstat USING btree (metric);


--
--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

