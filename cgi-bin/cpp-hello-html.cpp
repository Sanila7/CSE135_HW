#include <iostream>
#include <cstdlib>
#include <ctime>

int main() {
    // CGI header
    std::cout << "Content-Type: text/html\n\n";

    // Get environment variables
    const char* ip = std::getenv("REMOTE_ADDR");
    if (!ip) ip = "Unknown";

    // Get current time
    std::time_t now = std::time(nullptr);
    char timebuf[100];
    std::strftime(timebuf, sizeof(timebuf), "%Y-%m-%d %H:%M:%S", std::localtime(&now));

    // HTML output
    std::cout <<
R"(<!DOCTYPE html>
<html>
<head>
    <title>Hello HTML - C++</title>
</head>
<body>
    <h1>Hello from C++!</h1>

    <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>
    <p><strong>Language:</strong> C++</p>
    <p><strong>Generated at:</strong> )" << timebuf << R"(</p>
    <p><strong>Your IP address:</strong> )" << ip << R"(</p>

    <p><a href="/">Back to Home</a></p>
</body>
</html>)";

    return 0;
}
