CREATE PROCEDURE dbo.CargarIncapacidades
(
  @empresa udtInteger ,
  @tiponom udtInteger ,
  @row1 udtName ,
  @row2 udtName
)

AS
SET NOCOUNT ON

DECLARE @H CHAR(1) ,
        @nSetdatefirst INT ,
        @inicio DATETIME ,
        @fin DATETIME ,
        @periodo INT ,
        @ayo_operacion INT ,
        @fecha DATETIME


SELECT  @H = hind
FROM    dbo.relch_paramet
WHERE   empresa = @empresa

SELECT  @inicio = inicio ,
        @fin = cierre ,
        @periodo = periodo ,
        @ayo_operacion = ayo_operacion
FROM    dbo.periodos
WHERE   estatus = 'A'
  AND tiponom = @tiponom
  AND empresa = @empresa

SELECT  @nSetdatefirst = @@DATEFIRST
SET DATEFIRST 1

SET @Fecha = @inicio

WHILE @fecha <= @fin
    BEGIN
      DELETE  dbo.relch_registro
      FROM    dbo.deducciones AS a
      LEFT JOIN dbo.relch_registro AS r ON a.empresa = r.empresa
        AND a.codigo = r.codigo
        AND r.fecha = @fecha
        AND ( r.checada IS NULL
          OR r.num_conc = 108
        )
      WHERE   ( a.fchtermino >= @inicio
        AND a.fchinicio <= @fin
      )
        AND a.num_conc IN ( 109, 110, 111 )
        AND a.tiponom = @Tiponom
        AND a.empresa = @empresa

      INSERT  INTO dbo.relch_registro
      ( empresa ,
        codigo ,
        obs ,
        checada ,
        num_conc ,
        importe ,
        serial ,
        fecha ,
        marcafalta ,
        dias ,
        EoS,
        periodo ,
        ayo ,
        centro ,
        horario ,
        tiponom ,
        aplicara ,
        crf ,
        crf2
      )
      SELECT  a.empresa ,
      a.codigo ,
      ( CASE WHEN a.num_conc = 109 THEN 'Inc.EyG'
        WHEN a.num_conc = 110 THEN 'Inc.Mat'
        WHEN a.num_conc = 111 THEN 'Inc.RT'
        END ) AS obs ,
      '00:00:00' AS checada ,
      a.num_conc ,
      0 AS importe ,
      1 AS serial ,
      @fecha AS fecha ,
      '' AS mf ,
      1 ,
      1,
      @periodo ,
      @ayo_operacion ,
      l.centro ,
      CASE WHEN CASE WHEN @H = 'T' THEN e.horario
        ELSE l.horario
        END IS NULL THEN l.horario
        ELSE CASE WHEN @H = 'T' THEN e.horario
        ELSE l.horario
        END
      END ,
      l.tiponom ,
      @fecha ,
      @row1 ,
      @row2
      FROM    dbo.deducciones AS a
      LEFT JOIN dbo.relch_registro AS r ON a.empresa = r.empresa
        AND a.codigo = r.codigo
        AND r.fecha = @fecha
      INNER JOIN empleados AS e ON e.empresa = a.empresa
        AND e.codigo = a.codigo
      INNER JOIN dbo.llaves AS l ON l.empresa = e.empresa
        AND l.codigo = e.codigo
      WHERE   ( @fecha >= a.fchinicio
          AND @fecha <= a.fchtermino
        )
      AND a.num_conc IN ( 109, 110, 111 )
      AND a.tiponom = @Tiponom
      AND ( r.checada IS NULL
          OR r.num_conc = 108
        )
      AND a.empresa = @empresa
      SET @fecha = @fecha + 1

    END
    IF @H = 'T'
    BEGIN
      SET @Periodo = @Periodo + 1
      SELECT  @inicio = inicio ,
      @fin = cierre ,
      @ayo_operacion = ayo_operacion
      FROM    dbo.periodos
      WHERE   periodo = @periodo
      AND tiponom = @tiponom
      AND empresa = @empresa
      SET @fecha = @inicio
      WHILE @fecha <= @fin
        BEGIN
          DELETE  dbo.relch_registro
          FROM    dbo.deducciones AS a
          LEFT JOIN dbo.relch_registro AS r ON a.empresa = r.empresa
          AND a.codigo = r.codigo
          AND r.fecha = @fecha
          AND ( r.checada IS NULL
            OR r.num_conc = 108
          )
          WHERE   ( a.fchtermino >= @inicio
            AND a.fchinicio <= @fin
          )
          AND a.num_conc IN ( 109, 110, 111 )
          AND a.tiponom = @Tiponom
          AND a.empresa = @empresa

          INSERT  INTO dbo.relch_registro
          ( empresa ,
            codigo ,
            obs ,
            checada ,
            num_conc ,
            importe ,
            serial ,
            fecha ,
            marcafalta ,
            dias ,
            EoS,
            periodo ,
            ayo ,
            centro ,
            horario ,
            tiponom ,
            aplicara ,
            crf ,
            crf2
          )
          SELECT  a.empresa ,
          a.codigo ,
          ( CASE WHEN a.num_conc = 109
            THEN 'Inc.EyG'
            WHEN a.num_conc = 110
            THEN 'Inc.Mat'
            WHEN a.num_conc = 111
            THEN 'Inc.RT'
          END ) AS obs ,
          '00:00:00' AS checada ,
          a.num_conc ,
          0 AS importe ,
          1 AS serial ,
          @fecha AS fecha ,
          '' AS mf ,
          a.dias ,
          1,
          @periodo ,
          @ayo_operacion ,
          l.centro ,
          CASE WHEN CASE WHEN @H = 'T'
          THEN e.horario
          ELSE l.horario
          END IS NULL THEN l.horario
          ELSE CASE WHEN @H = 'T'
          THEN e.horario
          ELSE l.horario
          END
          END ,
          l.tiponom ,
          @fecha ,
          @row1 ,
          @row2
          FROM    dbo.deducciones AS a
          LEFT JOIN dbo.relch_registro AS r ON a.empresa = r.empresa
            AND a.codigo = r.codigo
            AND r.fecha = @fecha
          INNER JOIN dbo.empleados AS e ON e.empresa = a.empresa
            AND e.codigo = a.codigo
          INNER JOIN dbo.llaves AS l ON l.empresa = e.empresa
            AND l.codigo = e.codigo
          WHERE   ( @fecha >= a.fchinicio
            AND @fecha <= a.fchtermino
          )
          AND a.num_conc IN ( 109, 110, 111 )
          AND a.tiponom = @Tiponom
          AND ( r.checada IS NULL
            OR r.num_conc = 108
          )
          AND a.empresa = @empresa
          SET @fecha = @fecha + 1
      END
END

SELECT  'SUCCESSFULLY'
GO
