# familytree v0.0.0

This project provides software for creating and maintaining a family tree. It is based on the [The FamilySearch GEDCOM Specification](https://gedcom.io/specifications/FamilySearchGEDCOMv7.html#cb26-1) 7. The data is stored in SQL tables which can be generated from a GEDCOM 7 file and can generate a GEDCOM 7 file.

## SQL

### individuals

- id

  INTEGER AUTOINCREMENT

- xref

  TEXT

  This is the cross reference identifier of the INDI *Structure type*. A value of 72 would relate to GEDCOM like this: `0  @I72@ INDI`. Must be text as some GEDCOM ids have leading zeros and it may be convenient (although not necessary) to store the prefix (I in this case) in the database.

- resn

  TEXT ENUM(PRIVACY | NONE | NULL)

  RESN Structure Type. Restriction. g7 ENUM specification is different but includes PRIVACY.

- givn

  TEXT

  GIVN Structure Type. Given Name.

- surn

  TEXT

  SURN Structure Type. Surname.

- middle_names

  TEXT

  Can be used with givn and surn to create: *INDI-NAME -> PERSONAL_NAME_STRUCTURE -> PersonalName*. See PERSONAL_NAME_PIECES for example.

- sex

  TEXT ENUM(M | F | X | U)

  SEX Structure Type. Sex. X: Does not fit the typical definition of only Male or only Female. U: Cannot be determined from available sources

### families

- id

  INTEGER AUTOINCREMENT

- xref

  TEXT

  This is the cross reference identifier of the FAM *Structure type*. A value of 72 would relate to GEDCOM like this: `0  @F72@ FAM`. Must be text as some GEDCOM ids have leading zeros and it may be convenient (although not necessary) to store the prefix (F in this case) in the database.

  - husb

    TEXT
  
  - wife

    TEXT

## SQL Queries

SELECT i.givn, i.surn FROM individuals i LEFT JOIN families f ON i.famc = f.xref WHERE  f.xref = :xref;