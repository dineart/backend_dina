/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     04/05/2026 15:47:09                          */
/*==============================================================*/


drop table if exists BEASISWA;

drop table if exists KATEGORI_UKT;

drop table if exists KEUANGAN_MAHASISWA;

drop table if exists METODE_BAYAR;

drop table if exists TAGIHAN;

drop table if exists TRANSAKSI_PEMBAYARAN;

/*==============================================================*/
/* Table: BEASISWA                                              */
/*==============================================================*/
create table BEASISWA
(
   ID_BEASISWA          varchar(20) not null,
   NAMA_BEASISWA        varchar(100),
   PERSEN_POTONGAN      decimal(15,2),
   primary key (ID_BEASISWA)
);

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
   ID_BEASISWA          varchar(20),
   ID_MAHASISWA         varchar(20),
   SEMESTER             varchar(15),
   STATUS_AKTIF         varchar(20),
   primary key (ID_KEUANGAN_MHS)
);

/*==============================================================*/
/* Table: METODE_BAYAR                                          */
/*==============================================================*/
create table METODE_BAYAR
(
   ID_METODE            varchar(20) not null,
   NAMA_METODE          varchar(50),
   NO_REKENING          varchar(50),
   primary key (ID_METODE)
);

/*==============================================================*/
/* Table: TAGIHAN                                               */
/*==============================================================*/
create table TAGIHAN
(
   ID_TAGIHAN           varchar(20) not null,
   ID_KEUANGAN_MHS      varchar(20) not null,
   NO_INVOICE           varchar(50),
   NAMA_TAGIHAN         varchar(100),
   NOMOR_CICILAN        int,
   TOTAL_CICILAN        int,
   NOMINAL_CICILAN      decimal(15,2),
   DENDA                decimal(15,2),
   POTONGAN             decimal(15,2),
   TOTAL_TAGIHAN        decimal(15,2),
   TGL_JATUH_TEMPO      date,
   TGL_TAGIHAN          date,
   STATUS_BAYAR         varchar(25),
   primary key (ID_TAGIHAN)
);

/*==============================================================*/
/* Table: TRANSAKSI_PEMBAYARAN                                  */
/*==============================================================*/
create table TRANSAKSI_PEMBAYARAN
(
   ID_TRANSAKSI         varchar(20) not null,
   ID_TAGIHAN           varchar(20) not null,
   ID_METODE            varchar(20) not null,
   NO_VIRTUAL_ACCOUNT   varchar(50),
   JUMLAH_TAGIHAN       decimal(15,2),
   BIAYA_ADMIN          decimal(15,2),
   TOTAL_BAYAR          decimal(15,2),
   TGL_TRANSAKSI        datetime,
   TGL_KADALUWARSA      datetime,
   STATUS_TRANSAKSI     varchar(20),
   primary key (ID_TRANSAKSI)
);

alter table KEUANGAN_MAHASISWA add constraint FK_DIBERIKAN foreign key (ID_BEASISWA)
      references BEASISWA (ID_BEASISWA) on delete cascade on update cascade;

alter table KEUANGAN_MAHASISWA add constraint FK_MENENTUKAN foreign key (ID_KATEGORI)
      references KATEGORI_UKT (ID_KATEGORI) on delete cascade on update cascade;

alter table TAGIHAN add constraint FK_MEMILIKI foreign key (ID_KEUANGAN_MHS)
      references KEUANGAN_MAHASISWA (ID_KEUANGAN_MHS) on delete cascade on update cascade;

alter table TRANSAKSI_PEMBAYARAN add constraint FK_DIGUNAKAN foreign key (ID_METODE)
      references METODE_BAYAR (ID_METODE) on delete cascade on update cascade;

alter table TRANSAKSI_PEMBAYARAN add constraint FK_DILUNASI foreign key (ID_TAGIHAN)
      references TAGIHAN (ID_TAGIHAN) on delete cascade on update cascade;

