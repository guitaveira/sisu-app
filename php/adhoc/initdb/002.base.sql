--
-- PostgreSQL database dump
--

-- Dumped from database version 11.2
-- Dumped by pg_dump version 11.2

-- Started on 2020-04-12 18:08:01

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 277 (class 1255 OID 59031)
-- Name: AuxilioMesDelete(); Type: FUNCTION; Schema: public; Owner: -
--
-- Table: public.feedback

-- DROP TABLE public.feedback;

CREATE TABLE public.feedback
(
    id serial,
    nome text,
    email text,
    feedback text,
    CONSTRAINT pk_fb PRIMARY KEY (id)
)
    WITH (
        OIDS=FALSE
    );

