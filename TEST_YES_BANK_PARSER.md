# YES Bank Parser Testing Instructions

## Current Status
- YES Bank detection is WORKING (logs show "YES Bank DETECTED")
- Parser is being called but may not be extracting cheque numbers correctly
- Enhanced logging has been added

## To Test:
1. **Clear browser session** (or use incognito/private window)
2. **Re-upload the PDF**: `23-Jul-2025 To 23-Jan-2026.pdf`
3. **Check the log file**: `writable/logs/pdf_parser_YYYY-MM-DD.log`

## What to Look For in Logs:
- "YES Bank Parser: STARTED"
- "YES Bank Parser: Combined into X transaction lines"
- "Sample combined line 0: ..." (should show the transaction line)
- "YES Bank TRANSACTION #1: ... Cheque/Ref: 'YBL6019b31db08d438693ace553044723daOUT'"
- "Extracted cheque/reference from line start: '...'"

## Expected Results:
- Cheque numbers like `YBL6019b31db08d438693ace553044723daOUT` should appear in CHEQUE column
- They should NOT appear in PARTICULARS column
- ₹700 transaction should be in DEBIT column, not CREDIT

## If Still Not Working:
Check the log file and look for:
- Any "WARNING" or "ERROR" messages
- "Cheque/Ref: (empty)" messages
- "WARNING: First transaction has NO cheque number!"
