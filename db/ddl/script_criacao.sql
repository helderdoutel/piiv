CREATE DATABASE SIST_CARROS;

USE SIST_CARROS;

CREATE TABLE base2(
   id        SERIAL  NOT NULL PRIMARY KEY
  ,marca     VARCHAR(100) NOT NULL
  ,modelo    VARCHAR(200) NOT NULL
  ,descricao VARCHAR(200) NOT NULL
  ,acessorio VARCHAR(20) NOT NULL
  ,preco     VARCHAR(30)
);

CREATE TABLE marca(
   id_marca  SERIAL  NOT NULL PRIMARY KEY
  ,marca     VARCHAR(100) NOT NULL
);

CREATE TABLE modelo(
   id_modelo SERIAL NOT NULL PRIMARY KEY
  ,id_marca  int NOT NULL REFERENCES marca(id_marca)
  ,modelo    VARCHAR(200) NOT NULL
);

CREATE TABLE acessorio(
   id_acessorio        SERIAL  NOT NULL PRIMARY KEY
  ,acessorio VARCHAR(20) NOT NULL
);

CREATE TABLE carro(
   id_carro     SERIAL  NOT NULL PRIMARY KEY
  ,id_marca     VARCHAR(100) NOT NULL REFERENCES marca(id_marca)
  ,id_modelo    VARCHAR(200) NOT NULL REFERENCES modelo(id_modelo)
  ,descricao    VARCHAR(200) NOT NULL
  ,id_acessorio VARCHAR(20) NOT NULL REFERENCES acessorio(id_acessorio)
  ,preco        INT
);
