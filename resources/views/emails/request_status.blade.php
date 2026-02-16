<!DOCTYPE html>
<html>

<head>
    <title>Request Status Update</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: {{ $status === 'Approved' ? '#28a745' : '#dc3545' }};">Request {{ $status }}</h2>

        <p>Dear Requester,</p>

        <p>Your request has been <strong>{{ strtolower($status) }}</strong>.</p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Type:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ class_basename($record) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Number/ID:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $record->pr_number ?? $record->id }}</td>
            </tr>
            @if($reason)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Reason:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; color: #dc3545;">{{ $reason }}</td>
                </tr>
            @endif
        </table>

        <p>You can view the details by clicking the button below:</p>

        <a href="{{ $url }}"
            style="display: inline-block; padding: 10px 20px; background-color: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 10px;">View
            Request</a>

        <p style="margin-top: 20px; font-size: 0.9em; color: #666;">
            Thank you,<br>
            ISO Digital System
        </p>
    </div>
</body>

</html>