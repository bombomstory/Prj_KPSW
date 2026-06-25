func GetReportFiveDimensionsSingle(conn *sql.DB, provinceID, districtID, subDistrict, year int, toonStr, table, channel string, optionsList []models.XOption) (reportResultList models.FiveDimensions, err error) {
 var args []interface{}
 args = append(args, provinceID, year)

 filters := strings.Builder{}
 if districtID > 0 {
  filters.WriteString(` AND sa.AMP = ? `)
  args = append(args, districtID)
 }
 if subDistrict > 0 {
  filters.WriteString(` AND sa.TMP = ? `)
  args = append(args, subDistrict)
 }

 // เงื่อนไข empty field สำหรับ -1
 emptyChecks := make([]string, 0)
 for _, opt := range optionsList {
  if opt.FieldValue != -1 {
   emptyChecks = append(emptyChecks, fmt.Sprintf("(LTRIM(RTRIM(ISNULL(sa.%s, ''))) = '')", opt.FieldName))
  }
 }
 emptyCondition := strings.Join(emptyChecks, " AND ")

 reportResultList.SubTitle.Label = toonStr

 for _, option := range optionsList {
  var caseCondition string
  if option.FieldValue == -1 {
   caseCondition = fmt.Sprintf("WHEN %s THEN -1", emptyCondition)
  } else {
   caseCondition = fmt.Sprintf("WHEN LTRIM(RTRIM(sa.%s)) = '%d' THEN %d", option.FieldName, option.FieldValue, option.FieldValue)
  }

  query := fmt.Sprintf(`WITH a6_levels AS (
    SELECT * FROM (
        VALUES 
            (%d, '%s', '%s')
    ) AS t(field_value, xlabel, field_name)
),
raw_data AS (
    SELECT
        hr.detail,
        sa.HC,
        CASE 
            %s
        END AS field_value
    FROM %s sa
    JOIN hc_result hr ON sa.%s BETWEEN hr.min AND hr.max
    WHERE sa.JUN = ?
      AND sa.survey_year = ?
      %s
),
pivot_data AS (
    SELECT 
        field_value,
        COUNT(CASE WHEN detail = 'อยู่พอดี' THEN 1 END) AS [อยู่พอดี],
        COUNT(CASE WHEN detail = 'อยู่พอได้' THEN 1 END) AS [อยู่พอได้],
        COUNT(CASE WHEN detail = 'อยู่ยาก' THEN 1 END) AS [อยู่ยาก],
        COUNT(CASE WHEN detail = 'อยู่ลำบาก' THEN 1 END) AS [อยู่ลำบาก], 
        (COUNT(CASE WHEN detail = 'อยู่พอดี' THEN 1 END),
        +COUNT(CASE WHEN detail = 'อยู่พอได้' THEN 1 END),
        +COUNT(CASE WHEN detail = 'อยู่ยาก' THEN 1 END),
        +COUNT(CASE WHEN detail = 'อยู่ลำบาก' THEN 1 END)) as [รวม]  
    FROM raw_data
    GROUP BY field_value
)
SELECT 
    l.xlabel AS label,
    l.field_name AS field_name,
    l.field_value,
    ISNULL(p.[อยู่พอดี], 0) AS [อยู่พอดี],
    ISNULL(p.[อยู่พอได้], 0) AS [อยู่พอได้],
    ISNULL(p.[อยู่ยาก], 0) AS [อยู่ยาก],
    ISNULL(p.[อยู่ลำบาก], 0) AS [อยู่ลำบาก], 
    (ISNULL(p.[อยู่พอดี], 0) AS [อยู่พอดี],
    +ISNULL(p.[อยู่พอได้], 0) AS [อยู่พอได้],
    +ISNULL(p.[อยู่ยาก], 0) AS [อยู่ยาก],
    +ISNULL(p.[อยู่ลำบาก], 0) AS [อยู่ลำบาก]) as [รวม]  
FROM a6_levels l
LEFT JOIN pivot_data p ON l.field_value = p.field_value
ORDER BY 
    CASE 
        WHEN l.field_value = -1 THEN 999
        ELSE l.field_value
    END;
`, option.FieldValue, option.XLabel, option.FieldName, caseCondition, table, channel, filters.String())

  // fmt.Println("provinceID:", provinceID)
  // fmt.Println("year:", year)
  // fmt.Println("query:", query)

  var reportResult models.Dimensions
  reportResult, err = GetReportFiveDimensions(conn, query, toonStr, args)
  if err != nil {
   return
  }

  reportResultList.SubTitle.Normal += reportResult.Normal
  reportResultList.SubTitle.Fair += reportResult.Fair
  reportResultList.SubTitle.Difficult += reportResult.Difficult
  reportResultList.SubTitle.VeryHard += reportResult.VeryHard

  reportResultList.Data = append(reportResultList.Data, reportResult)
 }

 return
}