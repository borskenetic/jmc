# Official SF2 Excel template

The file `resources/templates/sf2/sf2-template.xlsx` is the DepEd **School Form 2** workbook. The app fills this template and downloads `.xlsx` — the closest match to the paper form.

## For teachers

1. Save your SF2 report in the app.
2. **Download Excel** — official `.xlsx` template filled with your data.
3. For a print-ready PDF, open the `.xlsx` in Excel or LibreOffice and use **Save as PDF** or **Print**.
4. Optional: use **Download PDF** in the app for a quick HTML-based preview (not identical to the Excel layout).

## Updating the template

If your division releases a newer SF2 `.xlsx`, replace `sf2-template.xlsx` and run:

```bash
php scripts/inspect-sf2-template.php
```

Adjust cell coordinates in `config/sf2.php` under `excel` if the layout changed.

## Reference files

Optional copies for comparison:

- `docs/sf2/filled-example.pdf` — filled sample
- Official blank from DepEd downloads

## Row limits (current template)

| Block | Rows | Max learners |
|-------|------|----------------|
| Male | 14–34 | 21 |
| Female | 36–60 | 25 |

If a class is larger, split across two reports or ask for a template with more rows.
