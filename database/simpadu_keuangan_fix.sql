/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     19/05/2026 08:19:58                          */
/*==============================================================*/


drop table if exists KATEGORI_UKT;

drop table if exists KEUANGAN_MAHASISWA;

drop table if exists TAGIHAN;

/*==============================================================*/
/* Table: KATEGORI_UKT                                          */
/*==============================================================*/
create table KATEGORI_UKT
(
   ID_KATEGORI          varchar(20) not null,
   ID_PRODI             varchar(20),
   JENJANG              varchar(20),
   GOLONGAN_UKT         varchar(15),
   NOMINAL_UKT          decimal(15,2),
   primary key (ID_KATEGORI)
);

/*==============================================================*/
/* Table: KEUANGAN_MAHASISWA                                    */
/*==============================================================*/
create table KEUANGAN_MAHASISWA
(
   ID_KEUANGAN_MHS      varchar(20) not null,
   ID_KATEGORI          varchar(20) not null,
   ID_MAHASISWA         varchar(20),
   SEMESTER             varchar(15),
   BEASISWA             varchar(20),
   STATUS_AKTIF         varchar(20),
   primary key (ID_KEUANGAN_MHS)
);

/*==============================================================*/
/* Table: TAGIHAN                                               */
/*==============================================================*/
create table TAGIHAN
(
   ID_TAGIHAN           varchar(20) not null,
   ID_KEUANGAN_MHS      varchar(20) not null,
   NO_INVOICE           varchar(50),
   NAMA_TAGIHAN         varchar(50),
   NOMOR_CICILAN        int,
   TOTAL_CICILAN        int,
   NOMINAL_CICILAN      decimal(15,2),
   POTONGAN             decimal(15,2),
   TOTAL_TAGIHAN        decimal(15,2),
   TGL_JATUH_TEMPO      date,
   TGL_TAGIHAN          date,
   STATUS_BAYAR         varchar(20),
   TGL_TRANSAKSI        date,
   primary key (ID_TAGIHAN)
);

alter table KEUANGAN_MAHASISWA add constraint FK_MENENTUKAN foreign key (ID_KATEGORI)
      references KATEGORI_UKT (ID_KATEGORI) on delete cascade on update cascade;

alter table TAGIHAN add constraint FK_MEMILIKI foreign key (ID_KEUANGAN_MHS)
      references KEUANGAN_MAHASISWA (ID_KEUANGAN_MHS) on delete cascade on update cascade;

