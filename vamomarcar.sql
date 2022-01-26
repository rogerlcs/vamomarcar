--
-- PostgreSQL database dump
--

-- Dumped from database version 13.5 (Ubuntu 13.5-2.pgdg20.04+1)
-- Dumped by pg_dump version 14.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: administra; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.administra (
    fk_usuario_codigo integer,
    fk_evento_codigo integer
);


--
-- Name: agenda_do_evento; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.agenda_do_evento (
    idagenda integer NOT NULL,
    fk_evento_codigo integer,
    fk_datas_codigo integer
);


--
-- Name: agenda_do_evento_idagenda_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.agenda_do_evento_idagenda_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: agenda_do_evento_idagenda_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.agenda_do_evento_idagenda_seq OWNED BY public.agenda_do_evento.idagenda;


--
-- Name: datas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.datas (
    codigo integer NOT NULL,
    data timestamp without time zone
);


--
-- Name: datas_codigo_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.datas_codigo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: datas_codigo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.datas_codigo_seq OWNED BY public.datas.codigo;


--
-- Name: estado; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.estado (
    id integer NOT NULL,
    nome character varying(75),
    uf character varying(5)
);


--
-- Name: estado_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.estado_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: estado_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.estado_id_seq OWNED BY public.estado.id;


--
-- Name: evento; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.evento (
    nome character varying(150),
    descricao text,
    nome_local character varying(100),
    codigo integer NOT NULL,
    prazo_votacao timestamp without time zone,
    prazo_sugestao timestamp without time zone,
    status_evento integer,
    data_marcada timestamp without time zone,
    endereco character varying(255),
    img text,
    id_criador integer
);


--
-- Name: evento_codigo_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.evento_codigo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: evento_codigo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.evento_codigo_seq OWNED BY public.evento.codigo;


--
-- Name: participa; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.participa (
    fk_usuario_codigo integer,
    fk_evento_codigo integer,
    status_convite integer
);


--
-- Name: usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario (
    nome character varying(255),
    senha character varying(255),
    fk_estado_id integer,
    codigo integer NOT NULL,
    email character varying(110),
    data_nascimento date,
    bio character varying(255),
    img text
);


--
-- Name: usuario_codigo_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_codigo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_codigo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_codigo_seq OWNED BY public.usuario.codigo;


--
-- Name: vota; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vota (
    fk_usuario_codigo integer,
    fk_idagenda integer
);


--
-- Name: agenda_do_evento idagenda; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agenda_do_evento ALTER COLUMN idagenda SET DEFAULT nextval('public.agenda_do_evento_idagenda_seq'::regclass);


--
-- Name: datas codigo; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datas ALTER COLUMN codigo SET DEFAULT nextval('public.datas_codigo_seq'::regclass);


--
-- Name: estado id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estado ALTER COLUMN id SET DEFAULT nextval('public.estado_id_seq'::regclass);


--
-- Name: evento codigo; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.evento ALTER COLUMN codigo SET DEFAULT nextval('public.evento_codigo_seq'::regclass);


--
-- Name: usuario codigo; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario ALTER COLUMN codigo SET DEFAULT nextval('public.usuario_codigo_seq'::regclass);


--
-- Name: agenda_do_evento agenda_do_evento_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agenda_do_evento
    ADD CONSTRAINT agenda_do_evento_pkey PRIMARY KEY (idagenda);


--
-- Name: datas datas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datas
    ADD CONSTRAINT datas_pkey PRIMARY KEY (codigo);


--
-- Name: estado estado_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estado
    ADD CONSTRAINT estado_pkey PRIMARY KEY (id);


--
-- Name: evento evento_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.evento
    ADD CONSTRAINT evento_pkey PRIMARY KEY (codigo);


--
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (codigo);


--
-- Name: administra fk_administra_1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.administra
    ADD CONSTRAINT fk_administra_1 FOREIGN KEY (fk_usuario_codigo) REFERENCES public.usuario(codigo) ON DELETE RESTRICT;


--
-- Name: administra fk_administra_2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.administra
    ADD CONSTRAINT fk_administra_2 FOREIGN KEY (fk_evento_codigo) REFERENCES public.evento(codigo) ON DELETE SET NULL;


--
-- Name: agenda_do_evento fk_agenda_do_evento_2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agenda_do_evento
    ADD CONSTRAINT fk_agenda_do_evento_2 FOREIGN KEY (fk_evento_codigo) REFERENCES public.evento(codigo);


--
-- Name: agenda_do_evento fk_agenda_do_evento_3; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agenda_do_evento
    ADD CONSTRAINT fk_agenda_do_evento_3 FOREIGN KEY (fk_datas_codigo) REFERENCES public.datas(codigo);


--
-- Name: evento fk_id_criador; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.evento
    ADD CONSTRAINT fk_id_criador FOREIGN KEY (id_criador) REFERENCES public.usuario(codigo);


--
-- Name: participa fk_participa_1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.participa
    ADD CONSTRAINT fk_participa_1 FOREIGN KEY (fk_usuario_codigo) REFERENCES public.usuario(codigo) ON DELETE RESTRICT;


--
-- Name: participa fk_participa_2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.participa
    ADD CONSTRAINT fk_participa_2 FOREIGN KEY (fk_evento_codigo) REFERENCES public.evento(codigo) ON DELETE SET NULL;


--
-- Name: usuario fk_usuario_2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_usuario_2 FOREIGN KEY (fk_estado_id) REFERENCES public.estado(id) ON DELETE CASCADE;


--
-- Name: vota fk_vota_1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vota
    ADD CONSTRAINT fk_vota_1 FOREIGN KEY (fk_usuario_codigo) REFERENCES public.usuario(codigo) ON DELETE RESTRICT;


--
-- Name: vota fk_vota_2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vota
    ADD CONSTRAINT fk_vota_2 FOREIGN KEY (fk_idagenda) REFERENCES public.agenda_do_evento(idagenda) ON DELETE SET NULL;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: -
--

GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: LANGUAGE plpgsql; Type: ACL; Schema: -; Owner: -
--

GRANT ALL ON LANGUAGE plpgsql TO postgres


--
-- PostgreSQL database dump complete
--

